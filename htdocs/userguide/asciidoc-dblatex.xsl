<?xml version="1.0" encoding="utf-8"?>
<!--
dblatex(1) XSL user stylesheet for asciidoc(1). See dblatex(1) -p option.

Doc for dblatex syntax: http://dblatex.sourceforge.net/doc/manual/
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
  <xsl:param name="doc.layout">coverpage toc frontmatter mainmatter index </xsl:param>

  <!-- TOC links in the titles, and in blue. -->
  <xsl:param name="latex.hyperparam">colorlinks,urlcolor=teclib-color-purple,linkcolor=teclib-color-purple,pdfstartview=FitH</xsl:param>

  <!-- TOC visiblity and depth -->
  <xsl:param name="doc.toc.show">1</xsl:param>
  <xsl:param name="titleabbrev.in.toc">0</xsl:param>
  <xsl:param name="toc.section.depth">2</xsl:param>

  <xsl:param name="doc.lot.show">0</xsl:param>
  <xsl:param name="doc.publisher.show">0</xsl:param>
  <xsl:param name="term.breakline">1</xsl:param>
  <!-- Show list of collaborators -->
  <xsl:param name="doc.collab.show">0</xsl:param>
  <xsl:param name="doc.section.depth">3</xsl:param>
  <xsl:param name="table.in.float">0</xsl:param>
<!--  <xsl:param name="table.default.tabstyle">longtable</xsl:param>-->
  <xsl:param name="default.table.width">newtbl.autowidth</xsl:param>
  <xsl:param name="newtbl.use.hhline" select="'0'"/>
  <xsl:param name="newtbl.format.thead">\color{teclib-color-purple}\bfseries</xsl:param>
  <xsl:param name="newtbl.bgcolor.thead">teclib-color-green-light</xsl:param>
  <xsl:param name="newtbl.format.tbody"></xsl:param>
  <xsl:param name="newtbl.autowidth">0</xsl:param>
  <xsl:param name="table.default.position">b</xsl:param>
  <xsl:param name="monoseq.hyphenation">0</xsl:param>
  <xsl:param name="latex.output.revhistory">0</xsl:param>
  <xsl:param name="latex.unicode.use">1</xsl:param>
  <xsl:param name="latex.encoding">utf8</xsl:param>
  <!-- This doesn't work, don't know why, see:
  http://dblatex.sourceforge.net/html/manual/apas03.html
  ./docbook-xsl/common.xsl
  -->
  
  <xsl:param name="doc.toc.show">
    <xsl:choose>
      <xsl:when test="/processing-instruction('asciidoc-toc')">
1
      </xsl:when>
      <xsl:otherwise>
0
      </xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  <!--
  <xsl:param name="doc.lot.show">
    <xsl:choose>
      <xsl:when test="/book">
figure,table,equation,example
      </xsl:when>
    </xsl:choose>
  </xsl:param>
  -->
  <!--<xsl:param name="doc.toc.show">1</xsl:param>-->

  <!--
    Override default literallayout template.
    See `./dblatex/dblatex-readme.txt`.
  -->
  <xsl:param name="xetex.font">
  <!--
    <xsl:text>\setmainfont{Liberation Sans}&#10;</xsl:text>
    <xsl:text>\setsansfont{Liberation Sans}&#10;</xsl:text>
    <xsl:text>\setmonofont{Liberation Mono}&#10;</xsl:text>
  -->
    <xsl:text>\setmainfont[Scale=0.8]{DejaVu Sans}&#10;</xsl:text>
    <xsl:text>\setsansfont[Scale=0.8]{DejaVu Sans}&#10;</xsl:text>
    <xsl:text>\setmonofont[Scale=0.8]{DejaVu Sans Mono}&#10;</xsl:text>
  </xsl:param>

  <xsl:param name="figure.note">admon/note.pdf</xsl:param>
  <xsl:param name="figure.tip">admon/tip.pdf</xsl:param>
  <xsl:param name="figure.warning">admon/warning.pdf</xsl:param>
  <xsl:param name="figure.caution">admon/caution.pdf</xsl:param>
  <xsl:param name="figure.important">admon/important.pdf</xsl:param>

  <xsl:template match="address|literallayout[@class!='monospaced']">
    <xsl:text>\begin{alltt}</xsl:text>
    <xsl:text>&#10;\normalfont{}&#10;</xsl:text>
    <xsl:apply-templates/>
    <xsl:text>&#10;\end{alltt}</xsl:text>
  </xsl:template>


  <xsl:template match="processing-instruction('asciidoc-pagebreak')">
    <!-- force hard pagebreak, varies from 0(low) to 4(high) -->
    <xsl:text>\pagebreak[4] </xsl:text>
    <xsl:apply-templates />
    <xsl:text>&#10;</xsl:text>
  </xsl:template>

  <xsl:template match="processing-instruction('asciidoc-br')">
    <xsl:text>\\&#10;</xsl:text>
  </xsl:template>

  <xsl:template match="phrase[@role='underline']">
    <xsl:call-template name="inline.underlineseq"/>
  </xsl:template>

  <xsl:template name="inline.underlineseq">
    <xsl:param name="content">
      <xsl:apply-templates/>
    </xsl:param>
    <xsl:text>\ul{</xsl:text>
    <xsl:copy-of select="$content"/>
    <xsl:text>}</xsl:text>
  </xsl:template>

  <xsl:template match="processing-instruction('asciidoc-hr')">
    <!-- draw a 444 pt line (centered) -->
    <xsl:text>\begin{center}&#10; </xsl:text>
    <xsl:text>\line(1,0){444}&#10; </xsl:text>
    <xsl:text>\end{center}&#10; </xsl:text>
  </xsl:template>

  <xsl:template match="productnumber" mode="docinfo">
    <xsl:text>\gdef\DBKcustomer{%&#10;</xsl:text>
    <xsl:value-of select="." />
    <xsl:text>}&#10;</xsl:text>
  </xsl:template>

  <xsl:template match="subtitle" mode="docinfo">
    <xsl:text>\gdef\DBKsubtitle{%&#10;</xsl:text>
    <xsl:apply-templates />
    <!--<xsl:value-of select="." />-->
    <xsl:text>}&#10;</xsl:text>
  </xsl:template>
  <xsl:template match="city" mode="docinfo">
    <xsl:text>\gdef\DBKsite{%&#10;</xsl:text>
    <xsl:value-of select="." />
    <xsl:text>}&#10;</xsl:text>
  </xsl:template>
<!--  <xsl:template match="date" mode="docinfo">
    <xsl:call-template name="datetime.format">
    <xsl:with-param name="date" select="date:date-time()"/>
    <xsl:with-param name="format" select="'A, B d, Y'"/>
    </xsl:call-template>
    <xsl:apply-templates/>
  </xsl:template>
-->
</xsl:stylesheet>

