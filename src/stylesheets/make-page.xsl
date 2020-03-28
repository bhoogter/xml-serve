<?xml version='1.0' encoding='ISO-8859-1'?>

<xsl:stylesheet 
version='1.0' 
xmlns:xsl='http://www.w3.org/1999/XSL/Transform' 
xmlns:php="http://php.net/xsl" 
xsl:extension-element-prefixes="php"
>
	<xsl:output method='html' cdata-section-elements="script style pre"/>
	<xsl:preserve-space elements="script style pre"/>

	<xsl:variable name='BenchmarkMAKE' select='0'/>
	<xsl:variable name='BenchmarkDIV' select='0'/>

	<xsl:variable name='gTitle' select="php:functionString('juniperGet', '//SITE/*/global/globaltitle')" />
	<xsl:variable name='bAppendSiteTitle' select="php:functionString('juniperGet', '//SITE/*/global/AppendSitetitle')" />

	<xsl:variable name='SRC' select='.' />
<!--	<xsl:variable name='CheckLoggedIn' select='php:functionString("ValidateLoginKey")'/> -->
	<xsl:variable name='CheckLoggedIn' select='1'/>


<!--
	<xsl:variable name='DoAdmin' select='php:functionString("DoPageAdmin")' />
	<xsl:variable name='DoLayout' select='php:functionString("DoPageLayout")' />
-->
	<xsl:variable name='DoAdmin' select='0' />
	<xsl:variable name='DoLayout' select='0' />

	<xsl:variable name='HandledElements' select='php:functionString("juniper_handled_elements")'/>

	<xsl:template match='/'>
		<xsl:variable name='make-page-start' select='php:functionString("BenchTime")'/>


<xsl:if test="false()">
	<table class='DEBUG' style='border:solid 1px black;'>
	<tr><td colspan='2' class='title'>make-page.xsl</td></tr>
	<tr><th>Var</th><th>Val</th></tr>
	<tr><td>pageID</td><td><xsl:value-of select='$pageID'/></td></tr>
	<tr><td>DoAdmin</td><td><xsl:value-of select='$DoAdmin'/></td></tr>
	<tr><td>DoLayout</td><td><xsl:value-of select='$DoLayout'/></td></tr>
	<tr><td>defTemplate</td><td><xsl:value-of select='$defTemplate'/></td></tr>
	<tr><td>speTemplate</td><td><xsl:value-of select='$speTemplate'/></td></tr>
	<tr><td>gTitle</td><td><xsl:value-of select='$gTitle'/></td></tr>
	<tr><td>bAppendSiteTitle</td><td><xsl:value-of select='$bAppendSiteTitle'/></td></tr>
	</table>
</xsl:if>

		<xsl:variable name='pTemplate' select='php:functionString("CurrentPageTemplateDOM")' />


		<xsl:variable name='temTitle' select='$pTemplate/*/subtitle' />
		<xsl:variable name='locTitle' select='/*/title' />
	    
		<xsl:variable name='pTitle'>
			<xsl:value-of select='$locTitle' />
			<xsl:if test='$temTitle!=""'> - <xsl:value-of select='$temTitle' /></xsl:if>
			<xsl:if test='$locTitle!="" and $bAppendSiteTitle!="" and $gTitle!=""'> - <xsl:value-of select='$gTitle' /></xsl:if>
		</xsl:variable>
	    
	    <xsl:variable name='pBody' select='$pTemplate//pagebody' />

<!--                    THIS DEBUG TABLE IS BEFORE THE CSS, SO IT IS COMMENTED OUT RATHER THAN PUT INTO AN XSL:IF      -->
<!--
<table border="1">
    <tr><td width="200px">defTemplate</td><td width="300px"><xsl:value-of select='$defTemplate' /></td></tr>
    <tr><td>gTitle</td><td><xsl:value-of select='$gTitle' /></td></tr>
    <tr><td>bAppendSiteTitle</td><td><xsl:value-of select='$bAppendSiteTitle' /></td></tr>
    <tr><td>Template</td><td><xsl:value-of select='php:functionString("CurrentPageTemplate")' /></td></tr>
    <tr><td>temTitle</td><td><xsl:value-of select='$temTitle' /></td></tr>
    <tr><td>locTitle</td><td><xsl:value-of select='$locTitle' /></td></tr>
    <tr><td>pTitle</td><td><xsl:value-of select='$pTitle' /></td></tr>
</table>
<xsl:message terminate='yes'>Debug Output Shown Above.</xsl:message>
-->
<html>
	<head>
		<xsl:variable name='pageGenerator' select="php:functionString('constant', 'SYSTEM_SDESC')" />
		<meta name="generator" content="{$pageGenerator}" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

		<xsl:variable name='gDesc' select='php:functionString("juniperGet", "//SITE/*/global/description")' />
		<xsl:variable name='pageDesc' select='php:functionString("ChooseBest", //desc, $pTemplate/desc, $gDesc)'/>
		<xsl:if test='string-length($pageDesc)!=0'>
			<meta name="description" content="{$pageDesc}"/>
		</xsl:if>

		<xsl:variable name='gKeywords' select='php:functionString("juniperGet", "//SITE/*/global/keywords")' />
		<xsl:variable name='pageKeywords' select='php:functionString("ChooseBest", //@keywords, $pTemplate/@keywords, $gKeywords)'/>
		<xsl:if test='string-length($pageKeywords)!=0'>
			<meta name="keywords" content="{$pageKeywords}"/> 
		</xsl:if>

		<title><xsl:value-of select='$pTitle' /></title>
<!--
			<xsl:for-each select='$SITE//*/global/css'>
				<link>
					<xsl:attribute name='rel'>stylesheet</xsl:attribute>
					<xsl:attribute name='type'>text/css</xsl:attribute>
					<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL",string(@src),"c", 1)' /></xsl:attribute>
				</link>
			</xsl:for-each>
-->
			<xsl:variable name='systemStyles' select="php:function('juniper_get_styles')" />
			<xsl:for-each select="$systemStyles/*/link" >
				<xsl:copy-of select='.'/>
			</xsl:for-each>

			<xsl:for-each select='$pTemplate/*/css'>
				<link>
					<xsl:attribute name='rel'>stylesheet</xsl:attribute>
					<xsl:attribute name='type'>text/css</xsl:attribute>
					<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL",string(@src),"c",1)' /></xsl:attribute>
				</link>
			</xsl:for-each>
			<xsl:for-each select='//*/css'>
				<link>
					<xsl:attribute name='rel'>stylesheet</xsl:attribute>
					<xsl:attribute name='type'>text/css</xsl:attribute>
					<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL",string(@src),"c", 1)' /></xsl:attribute>
				</link>
			</xsl:for-each>

<!--
			<xsl:for-each select='$SITE//*/global/rss'>
				<link>
					<xsl:attribute name='rel'>alternate</xsl:attribute>
					<xsl:attribute name='type'>application/rss+xml</xsl:attribute>
					<xsl:attribute name='href'><xsl:value-of select='@src' /></xsl:attribute>
				</link>
			</xsl:for-each>
-->
			<xsl:for-each select='$pTemplate/*/rss'>
				<link>
					<xsl:attribute name='rel'>alternate</xsl:attribute>
					<xsl:attribute name='type'>application/rss+xml</xsl:attribute>
					<xsl:attribute name='href'><xsl:value-of select='@src' /></xsl:attribute>
				</link>
			</xsl:for-each>
			<xsl:for-each select='//*/rss'>
				<link>
					<xsl:attribute name='rel'>alternate</xsl:attribute>
					<xsl:attribute name='type'>application/rss+xml</xsl:attribute>
					<xsl:attribute name='href'><xsl:value-of select='@src' /></xsl:attribute>
				</link>
			</xsl:for-each>

<!--
			<xsl:for-each select="$SITE//*/global/globalscript" >
				<script>
					<xsl:attribute name='type'><xsl:value-of select='php:functionString("DetectScriptType",string(@src))'/></xsl:attribute>
					<xsl:attribute name='src' ><xsl:value-of select='php:functionString("ExtendURL",@src,"j",1)' /></xsl:attribute>
				</script>
			</xsl:for-each>
-->
			<xsl:variable name='systemScripts' select="php:function('juniper_get_scripts')" />
			<xsl:for-each select="$systemScripts/*/script" >
				<xsl:copy-of select='.'/>
			</xsl:for-each>
			<xsl:for-each select='$pTemplate/*/script'>
				<script>
					<xsl:attribute name='type'><xsl:value-of select='php:functionString("DetectScriptType",string(@src))'/></xsl:attribute>
					<xsl:attribute name='src' ><xsl:value-of select='php:functionString("ExtendURL",@src,"j",1)' /></xsl:attribute>
					&#160;
				</script>
			</xsl:for-each>
			<xsl:for-each select='//*/script'>
				<script>
					<xsl:attribute name='type'><xsl:value-of select='php:functionString("DetectScriptType",string(@src))'/></xsl:attribute>
					<xsl:attribute name='src' ><xsl:value-of select='php:functionString("ExtendURL",@src,"j",1)' /></xsl:attribute>
					&#160;
				</script>
			</xsl:for-each>

<!--
            <xsl:for-each select='$SITE//*/global/meta'>
              <meta>
                <xsl:attribute name='name'><xsl:value-of select='@name' /></xsl:attribute>
                <xsl:attribute name='content'><xsl:value-of select='@content' /></xsl:attribute>
              </meta>
            </xsl:for-each>
-->
            <xsl:for-each select='$pTemplate/*/meta'>
              <meta>
                <xsl:attribute name='name'><xsl:value-of select='@name' /></xsl:attribute>
                <xsl:attribute name='content'><xsl:value-of select='@content' /></xsl:attribute>
              </meta>
            </xsl:for-each>
            <xsl:for-each select='//*/meta'>
              <meta>
                <xsl:attribute name='name'><xsl:value-of select='@name' /></xsl:attribute>
                <xsl:attribute name='content'><xsl:value-of select='@content' /></xsl:attribute>
              </meta>
            </xsl:for-each>

<!--
            <xsl:for-each select='$SITE//*/global/header'>
				<xsl:copy-of select='node()'/>
            </xsl:for-each>
-->
            <xsl:for-each select='$pTemplate/*/header'>
				<xsl:copy-of select='node()'/>
            </xsl:for-each>
            <xsl:for-each select='//*/header'>
				<xsl:copy-of select='node()'/>
            </xsl:for-each>
			<xsl:variable name='gIcon' select='php:functionString("juniperGet", "//PS/global/shortcuticon/@icon")' />
			<xsl:variable name='gIconImg' select='php:functionString("juniperGet", "//PS/global/shortcuticon/@img")' />
			<xsl:choose>
				<xsl:when test='string(/shortcuticon/@icon)!=""'>
					<link>
						<xsl:attribute name='rel'>shortcut icon</xsl:attribute>
						<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL", string(/shortcuticon/@icon),"i", 1)' /></xsl:attribute>
					</link>
				</xsl:when>
				<xsl:when test='string($pTemplate/*/shortcuticon/@icon)!=""'>
					<link>
						<xsl:attribute name='rel'>shortcut icon</xsl:attribute>
						<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL", string($Template/*/shortcuticon/@icon),"i",1)' /></xsl:attribute>
					</link>
				</xsl:when>
				<xsl:when test='string-length($gIcon)!=0'>
					<link>
						<xsl:attribute name='rel'>shortcut icon</xsl:attribute>
						<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL", string($gIcon),"i", 1)' /></xsl:attribute>
					</link>
				</xsl:when>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test='/shortcuticon/@img!=""'>
					<link>
						<xsl:attribute name='rel'>icon</xsl:attribute>
						<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL", string(/shortcuticon/@img),"i", 1)' /></xsl:attribute>
						<xsl:attribute name='type'><xsl:value-of select='php:functionString("ImageFormat",string(/shortcuticon/@img))' /></xsl:attribute>
					</link>
				</xsl:when>
				<xsl:when test='$pTemplate/*/shortcuticon/@img!=""'>
					<link>
						<xsl:attribute name='rel'>icon</xsl:attribute>
						<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL", string($Template/*/shortcuticon/@img),"i", 1)' /></xsl:attribute>
						<xsl:attribute name='type'><xsl:value-of select='php:functionString("ImageFormat",string($Template/*/shortcuticon/@img))' /></xsl:attribute>
					</link>
				</xsl:when>
			<xsl:when test='$gIconImg!=""'>
				<link>
					<xsl:attribute name='rel'>icon</xsl:attribute>
					<xsl:attribute name='href'><xsl:value-of select='php:functionString("ExtendURL", string($PS//global/shortcuticon/@img),"i", 1)' /></xsl:attribute>
					<xsl:attribute name='type'><xsl:value-of select='php:functionString("ImageFormat",string($PS//global/shortcuticon/@img))' /></xsl:attribute>
				</link>
			</xsl:when>
		</xsl:choose>
	</head>
	<body>
			<xsl:for-each select='$pTemplate/*/pagebody/*'>
				<xsl:call-template name="bodytemplate"/>
			</xsl:for-each>
	</body>
</html>
</xsl:template>
  
<xsl:template name='admindiv'>
	<xsl:variable name='divID' select='@id' />
	<xsl:variable name='PD' select='$SRC/*/content[@id=$divID]' />
	<xsl:variable name='pTemplate' select='php:function("CurrentPageTemplateDOM")'/>
	
<xsl:if test="false()">
	<table class='DEBUG' style='position: absolute;'>
		<tr><td colspan='2' class='title'>make-page.xsl - DEBUG-DIV1</td></tr>
		<tr><th>Var</th><th>Val</th></tr>
		<tr><td>divID</td><td><xsl:value-of select='$divID'/></td></tr>
	</table>
</xsl:if>

	<div>
		<xsl:attribute name='id'><xsl:value-of select='$divID'/></xsl:attribute>
		<xsl:for-each select='@*'>
			<xsl:choose>
				<xsl:when test='name()="admin"'></xsl:when>
				<xsl:when test='name()="id"'></xsl:when>
				<xsl:otherwise><xsl:copy-of select='.'/></xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>


	<xsl:for-each select='$PS//global/transform[@id=$divID]'>
		<xsl:sort select='@sort'/>
		<xsl:if test='string-length(@require)!=0 or (count($pTemplate/pagetemplate/transform[@id=$divID])=0 and count($SRC/*/content[@id=$divID])=0)'>
			<xsl:call-template name='admindiv-block'>
				<xsl:with-param name='divID'  select='$divID'/>
				<xsl:with-param name='source' select='"global"'/>
				<xsl:with-param name='src'    select='@src'/>
				<xsl:with-param name='type'   select='@type'/>
				<xsl:with-param name='render' select='@render'/>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>

	<xsl:for-each select='$pTemplate/pagetemplate/transform[@id=$divID]'>
		<xsl:sort select='@sort'/>
		<xsl:if test='string-length(@require)!=0 or count($SRC/*/content[@id=$divID])=0'>
			<xsl:call-template name='admindiv-block'>
				<xsl:with-param name='divID'  select='$divID'/>
				<xsl:with-param name='source' select='"template"'/>
				<xsl:with-param name='src'    select='@src'/>
				<xsl:with-param name='type'   select='@type'/>
				<xsl:with-param name='render' select='@render'/>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>

	<xsl:for-each select='$SRC/*/content[@id=$divID]'>
		<xsl:sort select='@sort'/>
		<xsl:call-template name='admindiv-block'>
			<xsl:with-param name='divID'  select='$divID'/>
			<xsl:with-param name='source' select='"page"'/>
			<xsl:with-param name='type'   select='@type'/>
			<xsl:with-param name='src'    select='@src'/>
			<xsl:with-param name='render' select='@render'/>
		</xsl:call-template>
	</xsl:for-each>

	</div>
</xsl:template>

	<xsl:template name='admindiv-block'>
		<xsl:param name='divID'/>
		<xsl:param name='source'/>
		<xsl:param name='type'/>
		<xsl:param name='src'/>
		<xsl:param name='render'/>

		<xsl:variable name='Template' select='php:function("CurrentPageTemplate")'/>
		<xsl:variable name='TemplateA' select='php:function("CurrentPageTemplate")'/>
		<xsl:variable name='pTemplate' select='php:function("CurrentPageTemplateDOM")'/>
		<xsl:variable name='objstart' select='php:functionString("BenchTime")'/>
				
		<xsl:variable name='newDivID'  select='php:functionString("newDivID", string($divID))'/>
	

<xsl:if test="false()">
	<table class='DEBUG' style='position: absolute;'>
		<tr><td colspan='2' class='title'>make-page.xsl - DEBUG-DIV</td></tr>
		<tr><th>Var</th><th>Val</th></tr>
		<tr><td>divID</td><td><xsl:value-of select='$divID'/></td></tr>
		<tr><td>Type</td><td><xsl:value-of select='$type'/></td></tr>
		<tr><td>Src</td><td><xsl:value-of select='$src'/></td></tr>
		<tr><td>Render</td><td><xsl:value-of select='$render'/></td></tr>
		<tr><td>Source</td><td><xsl:value-of select='$source'/></td></tr>
	</table>
</xsl:if>

			<div>
				<xsl:attribute name='id'><xsl:value-of select='$newDivID'/></xsl:attribute>

				<xsl:if test='$DoLayout="1"'>
					<xsl:call-template name='AddDivAdmin' select='node()'>
						<xsl:with-param name='source' select='$source'/>
						<xsl:with-param name='outerdivID' select='$divID'/>
						<xsl:with-param name='divID' select='$newDivID'/>
					</xsl:call-template>
				</xsl:if>

				<xsl:copy-of select='php:function("zsite_widget_render", $newDivID, $source, string($type), string($src), string($render), .)' />
<!--
			        <xsl:copy>
					<xsl:for-each select='@*'><xsl:copy-of select='.' /></xsl:for-each>
					<xsl:for-each select='text()'><xsl:copy-of select='.' /></xsl:for-each>
					<xsl:for-each select='*'>
						<xsl:call-template name='bodytemplate'/>
					</xsl:for-each>
			        </xsl:copy>
-->


			</div>
		<xsl:if test='number($BenchmarkDIV)>0'>
			<xsl:value-of disable-output-escaping='yes' select='php:functionString("BenchReport", string($objstart), $divID)'/>
		</xsl:if>
	</xsl:template>
  
	<xsl:template name='bodytemplate'>
		<xsl:variable name='N' select='name()' />
		<xsl:variable name='Ck' select='concat(",",name(),",")' />
		<xsl:variable name='HasNodeHandler' select='string-length($N)!=0 and contains($HandledElements, $Ck)' />
<!-- [Ck=<xsl:value-of select='$Ck' />, HNN=<xsl:value-of select='$HasNodeHandler' />] -->

		<xsl:choose>
			<xsl:when test='$HasNodeHandler'>
<!--				<xsl:copy-of select='php:function("juniper_render_node", $N, .)' /> -->
				<xsl:variable name='NodeResult' select='php:function("juniper_render_node", $N, .)' />

				<xsl:for-each select='$NodeResult'>
					<xsl:copy>
						<xsl:for-each select='@*'><xsl:copy-of select='.' /></xsl:for-each>
						<xsl:for-each select='node()'><xsl:call-template name='bodytemplate' /></xsl:for-each>
					</xsl:copy>
				</xsl:for-each>
<!--
				<xsl:copy-of select='$NodeResult'>
					<xsl:for-each select='@*'><xsl:copy-of select='.' /></xsl:for-each>
					<xsl:for-each select='node()'>
						<xsl:call-template name='bodytemplate' />
					</xsl:for-each>
				</xsl:copy-of>
-->
			</xsl:when>

			<xsl:when test='name()="script"'>
				<script>
					<xsl:for-each select='@*'><xsl:copy-of select='.' /></xsl:for-each>
					<xsl:text disable-output-escaping='yes'>
// &lt;![CDATA[</xsl:text>
					<xsl:copy-of select='./text()' />
					<xsl:text disable-output-escaping='yes'>//]]&gt;
</xsl:text>
				</script>
			</xsl:when>

			<xsl:otherwise>
				<xsl:copy>
					<xsl:for-each select='@*'><xsl:copy-of select='.' /></xsl:for-each>
					<xsl:for-each select='node()'>
						<xsl:call-template name='bodytemplate' />
					</xsl:for-each>
				</xsl:copy>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

 </xsl:stylesheet>