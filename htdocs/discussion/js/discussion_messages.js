/* Copyright (C) 2026 Laurent Destailleur <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * Page of messages of a channel (channel_messages.php).
 * Input area, same behaviour as the one of the AI assistant of Dolibarr: the textarea grows with its content,
 * Enter sends the message, Shift+Enter adds a new line. The message itself is sent by the click handler of
 * #sendmessage, defined into channel_messages.php, that also refuses an empty message.
 */
jQuery(function ($) {
	// Choice of the channel into the title of the page: open the messages of the selected channel
	$("#discussionchannelselect").on("change", function () {
		window.location.href = $(this).data("urlbase") + encodeURIComponent($(this).val());
	});

	var $input = $("#channelmessage");
	var $sendbutton = $("#sendmessage");
	if (!$input.length || !$sendbutton.length) {
		return;
	}

	// Bound with jQuery so the page can resize it after sending with .trigger("input")
	$input.on("input", function () {
		this.style.height = "auto";
		this.style.height = Math.min(this.scrollHeight, 150) + "px";
	});

	$input.on("keydown", function (event) {
		// isComposing: Enter that validates a word of an input method editor (Chinese, Japanese...) must not send
		if (event.key === "Enter" && !event.shiftKey && !(event.originalEvent && event.originalEvent.isComposing)) {
			event.preventDefault();
			$sendbutton.trigger("click");	// The click handler does not send an empty message
		}
	});
});
