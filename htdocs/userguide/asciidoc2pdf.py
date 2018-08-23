#!/usr/bin/python
#
# python pathto/teclib-make-report [--debug] file.asciidoc
#
import sys, os
from subprocess import Popen
import logging
from pprint import pprint, pformat

from argparse import ArgumentParser

# Set logging to DEBUG level
logging.basicConfig(level=logging.DEBUG)

# Get directory where this script is installed
scriptdir = os.path.dirname(os.path.realpath(__file__))

# Used by AsciiDoc to generate XML DocBook
asciidoc_conf =  os.path.normpath(os.path.join(scriptdir, "./asciidoc.conf"))

# Logos path
dblatex_logos = os.path.normpath(os.path.join(scriptdir,"./logos/"))
# Icons path
dblatex_icons = os.path.normpath(os.path.join(scriptdir,"./icons/"))



def execute_export(docfile=None, output=None , debug=False):
    if docfile is not None:
        (filebase, fileext) = os.path.splitext(docfile)
        xmlfile = "%s.xml" % filebase

        asciidoc_command = []
        asciidoc_command.extend(asciidoc_command_base)
        asciidoc_command.extend([docfile])
        if debug : asciidoc_command[1:1] = ['-v','-a trace']
        logging.info('Build xml from '+[docfile][0]+' with '+" ".join(asciidoc_command))
        p = Popen(" ".join(asciidoc_command),shell=True)
        r = p.wait()
        if r!=0 :
            logging.critical('Oops! something goes crazy with AsciiDoc ... (ie. retcode=%s)' % (str(r)))
            logging.critical('The command was \n%s' % (" ".join(asciidoc_command)))
            sys.exit(1)

        pdfcommand = []
        pdfcommand.extend(pdfcommand_base)
        if output is not None:
            pdfcommand.extend(['-o',output])
        pdfcommand.extend([xmlfile])
        if debug : pdfcommand.insert(1,'--debug')
        logging.info('Build pdf from '+[xmlfile][0]+' with '+" ".join(pdfcommand))
       
        p = Popen(" ".join(pdfcommand),shell=True)
        r = p.wait()
        if r!=0 :
            logging.critical('Oops! something goes crazy with DBLatex ... (ie. retcode=%s)' % (str(r)))
            logging.critical('The command was \n%s' % (pformat(pdfcommand)))
            sys.exit(2)

if __name__ == "__main__" :

    parser = ArgumentParser(description='Some script that spills out PDF file from an ASCIIDoc report')
    parser.add_argument('file', metavar="file", help='one Asciidoc file')
    parser.add_argument('--output', '-o' , metavar="output", help="file output name")
    parser.add_argument('--debug', action='store_true', help='add more Debug feature , especially the debug feature of DBLatex (cf. dblatex --help)')

    args = parser.parse_args()
	
    logging.info('Use local style sheets')
    dblatex_xsl = os.path.join(os.getcwd(),"asciidoc-dblatex/asciidoc-dblatex.xsl")
    dblatex_sty = os.path.join(os.getcwd(),"asciidoc-dblatex/asciidoc-dblatex.sty")
    
    pdfcommand_base = [
        'dblatex',
        '--type=pdf',
        #'--backend=pdftex',
        '--backend=xetex',
        '--xsl-user=%s' % (dblatex_xsl),
        '--texstyle=%s' % (dblatex_sty),
        '--texinputs=%s' % (dblatex_logos),
        '--texinputs=%s' % (dblatex_icons),
        '-V',
    ]

    asciidoc_command_base = [
        'asciidoc',
        '-v',
        '-f %s' % asciidoc_conf,
        '-b docbook'
    ]


    if os.path.exists(args.file) and os.path.isfile(args.file):
        if args.file.endswith('.asciidoc') or args.file.endswith('.txt') :
            execute_export(args.file, args.output, args.debug)
        else:
            logging.error("file %s doesn't seems to be a valid asciidoc file")
            sys.exit(2)
    else:
        logging.error("file %s doesn't exists or is not a file")
        sys.exit(1)

