#!/usr/bin/env bash
#
# A simple, configurable HTTP server written in bash.
#
# See LICENSE for licensing information.
#
# Original author: Avleen Vig, 2012
# Reworked by:     Josh Cartwright, 2012

warn() { echo "WARNING: $@" >&2; }

[ -r /etc/bashttpd.conf ] || {
   cat >/etc/bashttpd.conf <<'EOF'
#
# bashttpd.conf - configuration for bashttpd
#
# The behavior of bashttpd is dictated by the evaluation
# of rules specified in this configuration file.  Each rule
# is evaluated until one is matched.  If no rule is matched,
# bashttpd will serve a 500 Internal Server Error.
#
# The format of the rules are:
#    on_uri_match REGEX command [args]
#    unconditionally command [args]
#
# on_uri_match:
#   On an incoming request, the URI is checked against the specified
#   (bash-supported extended) regular expression, and if encounters a match the
#   specified command is executed with the specified arguments.
#
# unconditionally:
#   Always serve via the specified command.  Useful for catchall rules.
#
# Examples of rules:
#
# It is possible to somewhat easily write your own commands.  An example
# may help.  The following example will serve "Hello, $x!" whenever
# a client sends a request with the URI /deploy/$x:
#
# serve_deploy() {
#    add_response_header "Content-Type" "text/plain"
#    send_response_ok_exit <<< "Hello, $2!"
# }
#
on_uri_match '^/deploy/(.*)$' serve_deploy
on_uri_match '^/undeploy/(.*)$' serve_undeploy

# In all other cases
unconditionally serve_static_string 'Hello, world!  You can configure bashttpd by modifying bashttpd.conf.'

EOF
   warn "Created bashttpd.conf using defaults.  Please review it/configure before running bashttpd again."
   warn "Then run daemon with"
   warn "socat TCP4-LISTEN:8080,fork EXEC:.../remote_server.sh"
   exit 1
}

recv() { echo "< $@" >&2; }
send() { echo "> $@" >&2;
         printf '%s\r\n' "$*"; }

DATE=$(date +"%a, %d %b %Y %H:%M:%S %Z")
declare -a RESPONSE_HEADERS=(
      "Date: $DATE"
   "Expires: $DATE"
    "Server: Slash Bin Slash Bash"
)

add_response_header() {
   RESPONSE_HEADERS+=("$1: $2")
}

declare -a HTTP_RESPONSE=(
   [200]="OK"
   [400]="Bad Request"
   [403]="Forbidden"
   [404]="Not Found"
   [405]="Method Not Allowed"
   [500]="Internal Server Error"
)

send_response() {
   local code=$1
   send "HTTP/1.0 $1 ${HTTP_RESPONSE[$1]}"
   for i in "${RESPONSE_HEADERS[@]}"; do
      send "$i"
   done
   send
   while read -r line; do
      send "$line"
   done
}

send_response_ok_exit() { send_response 200; exit 0; }

fail_with() {
   send_response "$1" <<< "$1 ${HTTP_RESPONSE[$1]}"
   exit 1
}


# Deploy
serve_deploy() {
    add_response_header "Content-Type" "text/plain"
    
    export listofparam=`echo $2 | sed 's/%26/ /g'`

    send_response_ok_exit <<< "Hello, deploy $listofparam"
}

# Undeploy
serve_undeploy() {
    add_response_header "Content-Type" "text/plain"
    
    export listofparam=`echo $2 | sed 's/%26/ /g'`

    send_response_ok_exit <<< "Hello, deploy $listofparam"
}
 
 
serve_static_string() {
   add_response_header "Content-Type" "text/plain"
   send_response_ok_exit <<< "$1"
}

on_uri_match() {
   local regex=$1
   shift

   [[ $REQUEST_URI =~ $regex ]] && \
      "$@" "${BASH_REMATCH[@]}"
}

unconditionally() {
   "$@" "$REQUEST_URI"
}

# Request-Line HTTP RFC 2616 $5.1
read -r line || fail_with 400

# strip trailing CR if it exists
line=${line%%$'\r'}
recv "$line"

read -r REQUEST_METHOD REQUEST_URI REQUEST_HTTP_VERSION <<<"$line"

[ -n "$REQUEST_METHOD" ] && \
[ -n "$REQUEST_URI" ] && \
[ -n "$REQUEST_HTTP_VERSION" ] \
   || fail_with 400

# Only GET is supported at this time
[ "$REQUEST_METHOD" = "GET" ] || fail_with 405

declare -a REQUEST_HEADERS

while read -r line; do
   line=${line%%$'\r'}
   recv "$line"

   # If we've reached the end of the headers, break.
   [ -z "$line" ] && break

   REQUEST_HEADERS+=("$line")
done

#echo "source /etc/bashttpd.conf"
source "/etc/bashttpd.conf"
fail_with 500
