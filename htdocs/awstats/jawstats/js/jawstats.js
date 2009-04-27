/*
 * JAWStats 0.7 Web Statistics
 *
 * Copyright (c) 2009 Jon Combe (jawstats.com)
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

var oTranslation = {};
var oStatistics = {};
var dtLastUpdate = 0;
var sToolID;
var oPaging = {
  oKeywords:{ iCurrPage:0, iRowCount:0, iRowsPerPage:15, sSort:"freqDESC" },
  oKeyphrases:{ iCurrPage:0, iRowCount:0, iRowsPerPage:15, sSort:"freqDESC" }
};

// jQuery methods
$(document).ready(function() {
	var aCurrentView = g_sCurrentView.split(".");
	$("#menu").children("ul:eq(0)").children("li").addClass("off");
  $("#tab" + aCurrentView[0]).removeClass("off");
  DrawPage(g_sCurrentView);

  // change language mouseover
  $("#toolLanguageButton").mouseover(function() {
    $("#toolLanguageButton img").attr("src", "themes/" + sThemeDir + "/images/change_language_on.gif");
  });
  $("#toolLanguageButton").mouseout(function() {
    $("#toolLanguageButton img").attr("src", "themes/" + sThemeDir + "/images/change_language.gif");
  });
});

function AddLeadingZero(vValue, iLength) {
  sValue = vValue.toString();
  while (sValue.length < iLength) {
    sValue = ("0" + sValue);
  }
  return sValue;
}

function ChangeLanguage(sLanguage) {
  $("#loading").show();
  self.location.href = ("?config=" + g_sConfig + "&year=" + g_iYear + "&month=" + g_iMonth + "&view=" + g_sCurrentView + "&lang=" + sLanguage);
}

function ChangeMonth(iYear, iMonth) {
  $("#loading").show();
  self.location.href = ("?config=" + g_sConfig + "&year=" + iYear + "&month=" + iMonth + "&view=" + g_sCurrentView + "&lang=" + g_sLanguage);
}

function ChangeSite(sConfig) {
  $("#loading").show();
  self.location.href = ("?config=" + sConfig + "&year=" + g_iYear + "&month=" + g_iMonth + "&view=" + g_sCurrentView + "&lang=" + g_sLanguage);
}

function ChangeTab(oSpan, sPage) {
  $("#menu").children("ul:eq(0)").children("li").addClass("off");
  $(oSpan).parent().removeClass("off");
  DrawPage(sPage);
}

function CheckLastUpdate(oXML) {
  if (parseInt($(oXML).find('info').attr("lastupdate")) != g_dtLastUpdate) {
    var sURL = "?config=" + g_sConfig + "&year=" + g_iYear + "&month=" + g_iMonth + "&view=" + g_sCurrentView;
    self.location.href = sURL;
  }
}

function DisplayBandwidth(iBW) {
  iVal = iBW;

  iBW = (iBW / 1024);
  if (iBW < 1024) {
    return NumberFormat(iBW, 1) + "k";
  }
  iBW = (iBW / 1024);
  if (iBW < 1024) {
    return NumberFormat(iBW, 1) + "M";
  }
  iBW = (iBW / 1024);
  return NumberFormat(iBW, 1) + "G";
}

function DrawGraph(aItem, aValue, aInitial, sStyle) {
  var oGraph = new SWFObject("swf/" + sStyle + "_graph.swf", "SWFgraph", "100%", "100%", "8", "#ffffff");
  oGraph.addParam("wmode", "transparent");
  oGraph.addVariable("sItem", aItem.join(","));
  oGraph.addVariable("sValue", aValue.join(","));
  oGraph.addVariable("sInitial", aInitial.join(","));
  oGraph.addVariable("sColor", g_sColor);
  oGraph.addVariable("sShadowColor", g_sShadowColor);
  oGraph.write("graph");
}

function DrawGraph_AllMonths() {
  var aItem = [];
  var aValue = [];
  for (var iIndex in oStatistics.oAllMonths.aData) {
    aItem.push(Lang(gc_aMonthName[oStatistics.oAllMonths.aData[iIndex].dtDate.getMonth()].substr(0,3)) + " '" +
               (oStatistics.oAllMonths.aData[iIndex].dtDate.getFullYear()).toString().substr(2));
    aValue.push(oStatistics.oAllMonths.aData[iIndex].iVisits);
  }
  DrawGraph(aItem, aValue, [], "line");
}

function DrawGraph_ThisMonth() {
  var aItem = [];
  var aValue = [];
  var aInitial = [];

  // populate days
  var iDaysInMonth = (new Date(g_iYear, g_iMonth, 0)).getDate();
  var iDayOfWeek = (new Date(g_iYear, (g_iMonth - 1), 1)).getDay();
  for (var iDay = 0; iDay < iDaysInMonth; iDay++) {
    aItem.push(Lang((iDay + 1) + DateSuffix(iDay + 1)));
    aValue.push(0);
    aInitial.push(Lang(gc_aDayName[iDayOfWeek].substr(0, 3)));

    // day of week
    iDayOfWeek++;
    if (iDayOfWeek > 6) {
      iDayOfWeek = 0;
    }
  }

  // update values we know about
  for (var iIndex in oStatistics.oThisMonth.aData) {
    iDay = (oStatistics.oThisMonth.aData[iIndex].dtDate.getDate() - 1);
    aValue[iDay] = oStatistics.oThisMonth.aData[iIndex].iVisits;
  }
  DrawGraph(aItem, aValue, aInitial, "bar");
}

function DrawGraph_Time() {
  var aItem = [];
  var aValue = [];
  for (var iRow in oStatistics.oTime.aData) {
    oRow = oStatistics.oTime.aData[iRow];
    sHour = oRow.iHour;
    if (oRow.iHour < 10) {
      sHour = ("0" + sHour)
    }
    aItem.push(sHour);
    aValue.push(oRow.iPages);
  }
  DrawGraph(aItem, aValue, [], "line");
}

function DrawPage(sPage) {
	$("#content").fadeOut(g_iFadeSpeed, function() {
	  g_sCurrentView = sPage;
	  var aPage = sPage.split(".");
    switch (aPage[0]) {
      case "allmonths":
        if (typeof oStatistics.oAllMonths == "undefined") {
      	  PopulateData_AllMonths(sPage);
      	  return false;
        }
        PageLayout_AllMonths(aPage[1]);
        break;
      case "browser":
        if (typeof oStatistics.oBrowser == "undefined") {
      	  PopulateData_Browser(sPage);
      	  return false;
        }
        PageLayout_Browser(aPage[1]);
        break;
      case "country":
        if (typeof oStatistics.oCountry == "undefined") {
      	  PopulateData_Country(sPage);
      	  return false;
        }
        PageLayout_Country(aPage[1]);
        break;
      case "filetypes":
        if (typeof oStatistics.oFiletypes == "undefined") {
      	  PopulateData_Filetypes(sPage);
      	  return false;
        }
        PageLayout_Filetypes();
        break;
      case "keyphrases":
        if (typeof oStatistics.oKeyphrases == "undefined") {
      	  PopulateData_Keyphrases(sPage);
      	  return false;
        }
        PageLayout_Keyphrases(aPage[1]);
        break;
      case "keywords":
        if (typeof oStatistics.oKeywords == "undefined") {
      	  PopulateData_Keywords(sPage);
      	  return false;
        }
        PageLayout_Keywords(aPage[1]);
        break;
      case "os":
        if (typeof oStatistics.oOperatingSystems == "undefined") {
      	  PopulateData_OperatingSystems(sPage);
      	  return false;
        }
        PageLayout_OperatingSystems(aPage[1]);
        break;
      case "pagerefs":
        if (aPage[1] == "se") {
          if (typeof oStatistics.oPageRefsSE == "undefined") {
        	  PopulateData_PageRefsSE();
        	  return false;
          }
          PageLayout_PageRefsSE();
        } else {
          if (typeof oStatistics.oPageRefs == "undefined") {
        	  PopulateData_PageRefs(sPage, false);
        	  return false;
          }
          PageLayout_PageRefs(aPage[1]);
        }
        break;
      case "pages":
        if (typeof oStatistics.oPages == "undefined") {
      	  PopulateData_Pages(sPage);
      	  return false;
        }
        PageLayout_Pages(aPage[1]);
        break;
      case "robots":
        if (typeof oStatistics.oRobots == "undefined") {
      	  PopulateData_Robots(sPage);
      	  return false;
        }
        PageLayout_Robots();
        break;
case "searches":
  switch (aPage[1]) {
    case "keyphrasecloud":
      if (typeof oStatistics.oKeyphrases == "undefined") {
    	  PopulateData_Keyphrases(sPage);
    	  return false;
      }
      PageLayout_Searches(aPage[1]);
      break;
    case "keyphrases":
      if (typeof oStatistics.oKeyphrases == "undefined") {
    	  PopulateData_Keyphrases(sPage);
    	  return false;
      }
      PageLayout_Searches(aPage[1]);
      break;
    case "keywordcloud":
      if (typeof oStatistics.oKeywords == "undefined") {
    	  PopulateData_Keywords(sPage);
    	  return false;
      }
      PageLayout_Searches(aPage[1]);
      break;
    case "keywords":
      if (typeof oStatistics.oKeywords == "undefined") {
    	  PopulateData_Keywords(sPage);
    	  return false;
      }
      PageLayout_Searches(aPage[1]);
      break;
  }
  break;
      case "session":
        if (typeof oStatistics.oSession == "undefined") {
      	  PopulateData_Session();
      	  return false;
        }
        PageLayout_Session();
        break;
      case "status":
        if (aPage[1] == "404") {
          if (typeof oStatistics.oStatus404 == "undefined") {
        	  PopulateData_Status404(sPage);
        	  return false;
          }
        } else {
          if (typeof oStatistics.oStatus == "undefined") {
        	  PopulateData_Status(sPage);
        	  return false;
          }
        }
        PageLayout_Status(aPage[1]);
        break;
      case "thismonth":
        if (typeof oStatistics.oThisMonth == "undefined") {
      	  PopulateData_ThisMonth(sPage);
      	  return false;
        }
        PageLayout_ThisMonth(aPage[1]);
        break;
      case "time":
        if (typeof oStatistics.oTime == "undefined") {
      	  PopulateData_Time(sPage);
      	  return false;
        }
        PageLayout_Time();
        break;
    }
	});
}

function DrawPie(iTotal, aItem, aValue) {
  var oPie = new SWFObject("swf/pie.swf", "SWFpie", "100%", "100%", "8", "#ffffff");
  oPie.addParam("wmode", "transparent");
  oPie.addVariable("sTotal", iTotal);
  oPie.addVariable("sItem", encodeURIComponent(aItem.join(",")));
  oPie.addVariable("sValue", encodeURIComponent(aValue.join(",")));
  oPie.addVariable("sColor", g_sColor);
  oPie.addVariable("sShadowColor", g_sShadowColor);
  oPie.write("pie");
}

function DrawPie_Browser(sPage) {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;

  switch (sPage) {
    case "all":
      for (var iRow in oStatistics.oBrowser.aData) {
        if (iCount < 6) {
          aItem.push(oStatistics.oBrowser.aData[iRow].sBrowser);
          aValue.push(oStatistics.oBrowser.aData[iRow].iHits);
          iRunningTotal += oStatistics.oBrowser.aData[iRow].iHits;
          iCount++;
        }
      }
      if (oStatistics.oBrowser.iTotalHits > iRunningTotal) {
        aItem.push(Lang("Other Browsers"));
        aValue.push(oStatistics.oBrowser.iTotalHits - iRunningTotal);
      }
      DrawPie(oStatistics.oBrowser.iTotalHits, aItem, aValue);
      break;
    case "family":
      for (var iRow in oStatistics.oBrowser.aFamily) {
        if (iCount < 6) {
          if (oStatistics.oBrowser.aFamily[iRow].iHits > 0) {
            aItem.push(gc_aBrowserFamilyCaption[oStatistics.oBrowser.aFamily[iRow].sBrowser]);
            aValue.push(oStatistics.oBrowser.aFamily[iRow].iHits);
            iRunningTotal += oStatistics.oBrowser.aFamily[iRow].iHits;
            iCount++;
          }
        }
      }
      if (oStatistics.oBrowser.iTotalHits > iRunningTotal) {
        aItem.push(Lang("Other Browsers"));
        aValue.push(oStatistics.oBrowser.iTotalHits - iRunningTotal);
      }
      DrawPie(oStatistics.oBrowser.iTotalHits, aItem, aValue);
      break;
    default:
      // find family totals
      for (var iRow in aFamily) {
        if (aFamily[iRow].sBrowser == sPage) {
          iFamilyTotalHits = aFamily[iRow].iHits;
          break;
        }
      }

      // extract data
      for (var iRow in oStatistics.oBrowser.aData) {
        if ((iCount < 6) && (oStatistics.oBrowser.aData[iRow].sFamily == sPage)) {
          aItem.push(oStatistics.oBrowser.aData[iRow].sBrowser);
          aValue.push(oStatistics.oBrowser.aData[iRow].iHits);
          iRunningTotal += oStatistics.oBrowser.aData[iRow].iHits;
          iCount++;
        }
      }
      if (iFamilyTotalHits > iRunningTotal) {
        aItem.push(Lang("Other Versions"));
        aValue.push(iFamilyTotalHits - iRunningTotal);
      }
      DrawPie(iFamilyTotalHits, aItem, aValue);
      break;
  }
}

function DrawPie_Country(sContinent) {
  // get values
  if (typeof sContinent == "undefined") {
    iTotalPages = oStatistics.oCountry.iTotalPages;
  } else {
    iTotalPages = oStatistics.oCountry.oContinent[sContinent].iTotalPages;
  }
  aData = oStatistics.oCountry.aData;

  // build arrays
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in aData) {
    if (iCount < 6) {
      if ((typeof sContinent == "undefined") || (aData[iIndex].sContinent == sContinent)) {
        aItem.push(Lang(aData[iIndex].sCountryName));
        aValue.push(aData[iIndex].iPages);
        iRunningTotal += aData[iIndex].iPages;
        iCount++;
      }
    }
  }
  if (iTotalPages > iRunningTotal) {
    aItem.push(Lang("Other Countries"));
    aValue.push(iTotalPages - iRunningTotal);
  }
  DrawPie(iTotalPages, aItem, aValue);
}

function DrawPie_CountryContinent() {
  // this section is an anomaly whereby the continents need to be sorted by size before being passsed to the flash
  // thankfully there are only 6 (we are interested in)
  var aTemp = [];
  for (var sContinent in gc_aContinents) {
    aTemp.push({ "sContinent" : sContinent,
                 "iPages"     : oStatistics.oCountry.oContinent[sContinent].iTotalPages });
  }
  aTemp.sort(Sort_Pages);

  // pass across to simpler array format
  var iTotalPages = oStatistics.oCountry.iTotalPages;
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  for (var iIndex in aTemp) {
    aItem.push(Lang(aTemp[iIndex].sContinent));
    aValue.push(aTemp[iIndex].iPages);
    iRunningTotal += aTemp[iIndex].iPages;
  }
  if (iTotalPages > iRunningTotal) {
    aItem.push(Lang("Other"));
    aValue.push(iTotalPages - iRunningTotal);
  }
  DrawPie(iTotalPages, aItem, aValue);
}

function DrawPie_Filetypes() {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in oStatistics.oFiletypes.aData) {
    if (iCount < 6) {
      if (oStatistics.oFiletypes.aData[iIndex].sFiletype != "&nbsp;") {
        aItem.push(oStatistics.oFiletypes.aData[iIndex].sFiletype.toUpperCase() + ": " +
                   Lang(oStatistics.oFiletypes.aData[iIndex].sDescription));
      } else {
        aItem.push(Lang(oStatistics.oFiletypes.aData[iIndex].sDescription));
      }
      aValue.push(oStatistics.oFiletypes.aData[iIndex].iHits);
      iRunningTotal += oStatistics.oFiletypes.aData[iIndex].iHits;
    }
    iCount++;
  }
  if (oStatistics.oFiletypes.iTotalHits > iRunningTotal) {
    aItem.push(Lang("Other Filetypes"));
    aValue.push(oStatistics.oFiletypes.iTotalHits - iRunningTotal);
  }
  DrawPie(oStatistics.oFiletypes.iTotalHits, aItem, aValue);
}

function DrawPie_Keyphrases() {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in oStatistics.oKeyphrases.aData) {
    if (iCount < 6) {
      aItem.push(oStatistics.oKeyphrases.aData[iIndex].sPhrase);
      aValue.push(oStatistics.oKeyphrases.aData[iIndex].iFreq);
      iRunningTotal += oStatistics.oKeyphrases.aData[iIndex].iFreq;
    }
    iCount++;
  }
  if (oStatistics.oKeyphrases.iTotalFreq > iRunningTotal) {
    aItem.push(Lang("Other Keyphrases"));
    aValue.push(oStatistics.oKeyphrases.iTotalFreq - iRunningTotal);
  }
  DrawPie(oStatistics.oKeyphrases.iTotalFreq, aItem, aValue);
}

function DrawPie_Keywords() {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in oStatistics.oKeywords.aData) {
    if (iCount < 6) {
      aItem.push(oStatistics.oKeywords.aData[iIndex].sWord);
      aValue.push(oStatistics.oKeywords.aData[iIndex].iFreq);
      iRunningTotal += oStatistics.oKeywords.aData[iIndex].iFreq;
    }
    iCount++;
  }
  if (oStatistics.oKeywords.iTotalFreq > iRunningTotal) {
    aItem.push(Lang("Other Keywords"));
    aValue.push(oStatistics.oKeywords.iTotalFreq - iRunningTotal);
  }
  DrawPie(oStatistics.oKeywords.iTotalFreq, aItem, aValue);
}

function DrawPie_OperatingSystems(sPage) {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;

  switch (sPage) {
    case "all":
      for (var iRow in oStatistics.oOperatingSystems.aData) {
        if (iCount < 6) {
          aItem.push(oStatistics.oOperatingSystems.aData[iRow].sOperatingSystem);
          aValue.push(oStatistics.oOperatingSystems.aData[iRow].iHits);
          iRunningTotal += oStatistics.oOperatingSystems.aData[iRow].iHits;
          iCount++;
        }
      }
      if (oStatistics.oOperatingSystems.iTotalHits > iRunningTotal) {
        aItem.push(Lang("Other Operating Systems"));
        aValue.push(oStatistics.oOperatingSystems.iTotalHits - iRunningTotal);
      }
      DrawPie(oStatistics.oOperatingSystems.iTotalHits, aItem, aValue);
      break;
    case "family":
      for (var iRow in oStatistics.oOperatingSystems.aFamily) {
        if (iCount < 6) {
          if (oStatistics.oOperatingSystems.aFamily[iRow].iHits > 0) {
            aItem.push(gc_aOSFamilyCaption[oStatistics.oOperatingSystems.aFamily[iRow].sOperatingSystem]);
            aValue.push(oStatistics.oOperatingSystems.aFamily[iRow].iHits);
            iRunningTotal += oStatistics.oOperatingSystems.aFamily[iRow].iHits;
            iCount++;
          }
        }
      }
      if (oStatistics.oOperatingSystems.iTotalHits > iRunningTotal) {
        aItem.push(Lang("Other Operating Systems"));
        aValue.push(oStatistics.oOperatingSystems.iTotalHits - iRunningTotal);
      }
      DrawPie(oStatistics.oOperatingSystems.iTotalHits, aItem, aValue);
      break;
    default:
      // find family totals
      for (var iRow in oStatistics.oOperatingSystems.aFamily) {
        if (oStatistics.oOperatingSystems.aFamily[iRow].sBrowser == sPage) {
          iFamilyTotalHits = oStatistics.oOperatingSystems.aFamily[iRow].iHits;
          break;
        }
      }

      // extract data
      for (var iRow in oStatistics.oOperatingSystems.aData) {
        if ((iCount < 6) && (oStatistics.oOperatingSystems.aData[iRow].sFamily == sPage)) {
          aItem.push(oStatistics.oOperatingSystems.aData[iRow].sOperatingSystem);
          aValue.push(oStatistics.oOperatingSystems.aData[iRow].iHits);
          iRunningTotal += oStatistics.oOperatingSystems.aData[iRow].iHits;
          iCount++;
        }
      }
      if (iFamilyTotalHits > iRunningTotal) {
        aItem.push(Lang("Other Versions"));
        aValue.push(iFamilyTotalHits - iRunningTotal);
      }
      DrawPie(iFamilyTotalHits, aItem, aValue);
      break;
  }
}

function DrawPie_PageRefs(sPage) {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;

  // switch view
  switch (sPage) {
    case "all":
    case "top10":
    case "top50":
      var aData = oStatistics.oPageRefs.aData;
      var sVarName = "sURL";
      break;
    case "domains":
      var aData = oStatistics.oPageRefs.aDataDomain;
      var sVarName = "sVisibleURL";
      break;
  }

  // loop through data
  for (var iIndex in aData) {
    if (iCount < 6) {
      aItem.push(aData[iIndex][sVarName]);
      aValue.push(aData[iIndex].iPages);
      iRunningTotal += aData[iIndex].iPages;
    }
    iCount++;
  }
  if (oStatistics.oPageRefs.iTotalPages > iRunningTotal) {
    aItem.push(Lang("Other Referrers"));
    aValue.push(oStatistics.oPageRefs.iTotalPages - iRunningTotal);
  }
  DrawPie(oStatistics.oPageRefs.iTotalPages, aItem, aValue);
}

function DrawPie_PageRefsSE(sPage) {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  var aData = oStatistics.oPageRefsSE.aData;

  // loop through data
  for (var iIndex in aData) {
    if (iCount < 6) {
      aItem.push(aData[iIndex].sReferrer);
      aValue.push(aData[iIndex].iPages);
      iRunningTotal += aData[iIndex].iPages;
    }
    iCount++;
  }
  if (oStatistics.oPageRefsSE.iTotalPages > iRunningTotal) {
    aItem.push(Lang("Other Search Engines"));
    aValue.push(oStatistics.oPageRefsSE.iTotalPages - iRunningTotal);
  }
  DrawPie(oStatistics.oPageRefsSE.iTotalPages, aItem, aValue);
}

function DrawPie_Pages(aData, iTotal, sItemName) {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in aData) {
    if (iCount < 6) {
      aItem.push(aData[iIndex].sURL);
      aValue.push(aData[iIndex][sItemName]);
      iRunningTotal += aData[iIndex][sItemName];
    }
    iCount++;
  }
  if (iTotal > iRunningTotal) {
    aItem.push(Lang("Other URLs"));
    aValue.push(iTotal - iRunningTotal);
  }
  DrawPie(iTotal, aItem, aValue);
}

function DrawPie_Robots() {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in oStatistics.oRobots.aData) {
    if (iCount < 6) {
      aItem.push(oStatistics.oRobots.aData[iIndex].sRobot);
      aValue.push(oStatistics.oRobots.aData[iIndex].iHits);
      iRunningTotal += oStatistics.oRobots.aData[iIndex].iHits;
    }
    iCount++;
  }
  if (oStatistics.oRobots.iTotalHits > iRunningTotal) {
    aItem.push(Lang("Other Spiders"));
    aValue.push(oStatistics.oRobots.iTotalHits - iRunningTotal);
  }
  DrawPie(oStatistics.oRobots.iTotalHits, aItem, aValue);
}

function DrawPie_Session() {
  var aItem = [Lang("0 seconds - 30 seconds"), Lang("30 seconds - 2 minutes"), Lang("2 minutes - 5 minutes"), Lang("5 minutes - 15 minutes"), Lang("15 minutes - 30 minutes"), Lang("30 minutes - 1 hour"), Lang("More than 1 hour")];
  var aValue = [oStatistics.oSession.aData.s0s30s,
                oStatistics.oSession.aData.s30s2mn,
                oStatistics.oSession.aData.s2mn5mn,
                oStatistics.oSession.aData.s5mn15mn,
                oStatistics.oSession.aData.s15mn30mn,
                oStatistics.oSession.aData.s30mn1h,
                oStatistics.oSession.aData.s1h];
  DrawPie(oStatistics.oSession.iTotalFreq, aItem, aValue);
}

function DrawPie_Status() {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in oStatistics.oStatus.aData) {
    if (iCount < 6) {
      if (oStatistics.oStatus.aData[iIndex].sDescription != "&nbsp;") {
        aItem.push(oStatistics.oStatus.aData[iIndex].sCode + ": " +
                   Lang(oStatistics.oStatus.aData[iIndex].sDescription));
      } else {
        aItem.push(oStatistics.oStatus.aData[iIndex].sCode);
      }
      aValue.push(oStatistics.oStatus.aData[iIndex].iHits);
      iRunningTotal += oStatistics.oStatus.aData[iIndex].iHits;
    }
    iCount++;
  }
  if (oStatistics.oStatus.iTotalHits > iRunningTotal) {
    aItem.push(Lang("Other Status Codes"));
    aValue.push(oStatistics.oStatus.iTotalHits - iRunningTotal);
  }
  DrawPie(oStatistics.oStatus.iTotalHits, aItem, aValue);
}

function DrawPie_Status404() {
  var aItem = [];
  var aValue = [];
  var iRunningTotal = 0;
  var iCount = 0;
  for (var iIndex in oStatistics.oStatus404.aData) {
    if (iCount < 6) {
      aItem.push(oStatistics.oStatus404.aData[iIndex].sURL.replace(/&#8203;/g, ""));
      aValue.push(oStatistics.oStatus404.aData[iIndex].iHits);
      iRunningTotal += oStatistics.oStatus404.aData[iIndex].iHits;
    }
    iCount++;
  }
  if (oStatistics.oStatus404.iTotalHits > iRunningTotal) {
    aItem.push("Other URLs");
    aValue.push(oStatistics.oStatus404.iTotalHits - iRunningTotal);
  }
  DrawPie(oStatistics.oStatus404.iTotalHits, aItem, aValue);
}

function DrawSubMenu(sMenu, sSelected) {
  // choose object
  switch (sMenu) {
    case "allmonths":
      oMenu = oSubMenu["AllMonths"];
      break;
    case "browser":
      oMenu = oSubMenu["Browser"];
      break;
    case "country":
      oMenu = oSubMenu["Country"];
      break;
    case "keyphrases":
      oMenu = oSubMenu["Keyphrases"];
      break;
    case "keywords":
      oMenu = oSubMenu["Keywords"];
      break;
    case "os":
      oMenu = oSubMenu["OS"];
      break;
    case "pagerefs":
      oMenu = oSubMenu["PageRefs"];
      break;
    case "pages":
      oMenu = oSubMenu["Pages"];
      break;
    case "searches":
      oMenu = oSubMenu["Searches"];
      break;
    case "status":
      oMenu = oSubMenu["Status"];
      break;
    case "thismonth":
      oMenu = oSubMenu["ThisMonth"];
      break;
    default:
      return "Bad SubMenu Name";
  }

  // create menu
  var aMenu = [];
  for (sLabel in oMenu) {
    if (sSelected == sLabel) {
      aMenu.push("<span class=\"submenuselect\" onclick=\"DrawPage('" + oMenu[sLabel] + "')\">" + Lang(sLabel) + "</span>");
    } else {
      aMenu.push("<span class=\"submenu\" onclick=\"DrawPage('" + oMenu[sLabel] + "')\">" + Lang(sLabel) + "</span>");
    }
  }
  return ("<div id=\"submenu\">" + aMenu.join(" | ") + "</div>");
}

function DrawTable_AllMonths(sPage) {
  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>";
  if (sPage == "all") {
    sHTML += "<th width=\"16%\">" + Lang("Month") + "</th>";
  } else {
    sHTML += "<th width=\"16%\">" + Lang("Year") + "</th>";
  }
  sHTML += "<th width=\"12%\">" + Lang("Total Visitors") + "</th>" +
           "<th width=\"12%\">" + Lang("Visitors per Day") + "</th>" +
           "<th width=\"12%\">" + Lang("Unique Visitors") + "</th>" +
           "<th width=\"12%\">" + Lang("Unique Ratio") + "</th>" +
           "<th width=\"12%\">" + Lang("Pages") + "</th>" +
           "<th width=\"12%\">" + Lang("Hits") + "</th>" +
           "<th width=\"12%\" class=\"noborder\">" + Lang("BW") + "</th>" +
           "</tr></thead>\n" +
           "<tbody>";

  // create table body
  aHTML = new Array();
  var iTotalVisits   = 0;
  var iTotalUniques  = 0;
  var iTotalPages    = 0;
  var iTotalHits     = 0;
  var iTotalBW       = 0;
  var iAnnualVisits  = 0;
  var iAnnualUniques = 0;
  var iAnnualPages   = 0;
  var iAnnualHits    = 0;
  var iAnnualBW      = 0;
  var iCurrentYear   = oStatistics.oAllMonths.aData[0].iYear;

  for (var iRow in oStatistics.oAllMonths.aData) {
    oRow = oStatistics.oAllMonths.aData[iRow];

    // create single values
    var iVisits      = parseInt(oRow.iVisits);
    var iUniques     = parseInt(oRow.iUniques);
    var iPages       = parseInt(oRow.iPages);
    var iHits        = parseInt(oRow.iHits);
    var iBW          = parseInt(oRow.iBW);
    var iDaysInMonth = parseFloat(oRow.iDaysInMonth);

    // sum totals
    iTotalVisits   += iVisits;
    iTotalUniques  += iUniques;
    iTotalPages    += iPages;
    iTotalHits     += iHits;
    iTotalBW       += iBW;
    iAnnualVisits  += iVisits;
    iAnnualUniques += iUniques;
    iAnnualPages   += iPages;
    iAnnualHits    += iHits;
    iAnnualBW      += iBW;
    iCurrentYear   = oRow.iYear;

    // create table
    switch (sPage) {
      case "all":
        if ((g_iMonth == oRow.iMonth) && (g_iYear == oRow.iYear)) {
          var sHTMLRow = "<tr class=\"highlight\">";
        } else {
          var sHTMLRow = "<tr>";
        }
        sHTMLRow += "<td><span class=\"hidden\">" + oRow.dtDate.valueOf() + "</span>" + Lang(gc_aMonthName[oRow.iMonth - 1]) + " " + oRow.iYear + "</td>" +
                    "<td class=\"right\">" + NumberFormat(iVisits, 0) + "</td>" +
                    "<td class=\"right\">" + NumberFormat((iVisits / iDaysInMonth), 1) + "</td>" +
                    "<td class=\"right\">" + NumberFormat(iUniques) + "</td>";
        if (iVisits > 0) {
          sHTMLRow += "<td class=\"right\">" + NumberFormat(((iUniques / iVisits) * 100), 0) + "%</td>";
        } else {
          sHTMLRow += "<td class=\"right\">0%</td>";
        }
        sHTMLRow += "<td class=\"right\">" + NumberFormat(iPages, 0) + "</td>" +
                    "<td class=\"right\">" + NumberFormat(iHits, 0) + "</td>" +
                    "<td class=\"right\">" + DisplayBandwidth(iBW) + "</td>" +
                    "</tr>\n";
        aHTML.push(sHTMLRow);
        break;
      case "year":
        //if ((iCurrentYear != oRow.iYear) || (iRow == (oStatistics.oAllMonths.aData.length - 1))) {
        if ((oRow.iMonth == 12) || (iRow == (oStatistics.oAllMonths.aData.length - 1))) {
          var sHTMLRow = "<tr>" +
                         "<td>" + iCurrentYear + "</td>" +
                         "<td class=\"right\">" + NumberFormat(iAnnualVisits) + "</td>" +
                         "<td class=\"right\">" + NumberFormat((iAnnualVisits / oStatistics.oAllMonths.aYearDayCount[iCurrentYear]), 1) + "</td>" +
                         "<td class=\"right\">" + NumberFormat(iAnnualUniques, 0) + "</td>";
          if (iAnnualVisits > 0) {
            sHTMLRow += "<td class=\"right\">" + NumberFormat(((iAnnualUniques / iAnnualVisits) * 100), 0) + "%</td>";
          } else {
            sHTMLRow += "<td class=\"right\">0%</td>";
          }
          sHTMLRow += "<td class=\"right\">" + NumberFormat(iAnnualPages, 0) + "</td>" +
                      "<td class=\"right\">" + NumberFormat(iAnnualHits, 0) + "</td>" +
                      "<td class=\"right\">" + DisplayBandwidth(iAnnualBW) + "</td>" +
                      "</tr>\n";
          aHTML.push(sHTMLRow);

          // reset values
          iAnnualVisits  = 0;
          iAnnualUniques = 0;
          iAnnualPages   = 0;
          iAnnualHits    = 0;
          iAnnualBW      = 0;
        }
        break;
    }
  }

  // output
  if (aHTML.length > 0) {
    sHTML = (sHTML + aHTML.join("\n") + "</tbody><tfoot><tr>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalVisits, 0) + "</td>" +
             "<td class=\"noborder right\">&nbsp;</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalUniques, 0) + "</td>" +
             "<td class=\"noborder right\">&nbsp;</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalPages, 0) + "</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalHits, 0) + "</td>" +
             "<td class=\"noborder right\">" + DisplayBandwidth(iTotalBW) + "</td>" +
             "</tr></tfoot></table>")
    return ( [ true, sHTML ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"7\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_Browser(sPage) {
  // get values
  iTotalHits      = oStatistics.oBrowser.iTotalHits;
  aData           = oStatistics.oBrowser.aData;
  aFamily         = oStatistics.oBrowser.aFamily;

  // create table body
  aHTML = new Array();
  switch (sPage) {
    case "all":
      // create header
      var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
                  "<thead><tr>" +
                  "<th width=\"1\">&nbsp;</th>" +
                  "<th>" + Lang("Browser") + "</th>" +
                  "<th>" + Lang("Hits") + "</th>" +
    				      "<th class=\"noborder\">&nbsp;</th>" +
                  "</tr></thead>\n" +
                  "<tbody>";

      // create output
      for (var iRow in aData) {
        iPercent = ((aData[iRow].iHits / iTotalHits) * 100);
        aHTML.push("<tr>" +
                   "<td class=\"browserlogo\"><img src=\"themes/" + sThemeDir + "/browsers/" + aData[iRow].sFamily.replace(" ", "").replace("-", "").replace("\\", "").toLowerCase() + ".gif\" alt=\"" + aData[iRow].sFamily + "\" /></td>" +
                   "<td>" + aData[iRow].sBrowser + "</td>" +
                   "<td class=\"right\">" + NumberFormat(aData[iRow].iHits, 0) + "</td>" +
  			           "<td class=\"noborder right\">" + iPercent.toFixed(1) + "%</td>" +
                   "</tr>\n");
      }
      break;
    case "family":
      // create header
      var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
                  "<thead><tr>" +
                  "<th width=\"1\">&nbsp;</th>" +
                  "<th>" + Lang("Browser Family") + "</th>" +
                  "<th>" + Lang("Hits") + "</th>" +
    				      "<th class=\"noborder\">&nbsp;</th>" +
                  "</tr></thead>\n" +
                  "<tbody>";

      // create output
      for (var iRow in aFamily) {
        if (aFamily[iRow].iHits > 0) {
          iPercent = ((aFamily[iRow].iHits / iTotalHits) * 100);
          aHTML.push("<tr>" +
                     "<td class=\"browserlogo\"><img src=\"themes/" + sThemeDir + "/browsers/" + aFamily[iRow].sBrowser.replace(" ", "").replace("-", "").replace("\\", "").toLowerCase() + ".gif\" alt=\"" + aFamily[iRow].sBrowser + "\"/></td>" +
                     "<td>" + gc_aBrowserFamilyCaption[aFamily[iRow].sBrowser] + " &nbsp;<span class=\"fauxlink tiny\" onclick=\"DrawPage('browser." +
                     aFamily[iRow].sBrowser  + "');\">&raquo;</span>" + "</td>" +
                     "<td class=\"right\">" + NumberFormat(aFamily[iRow].iHits, 0) + "</td>" +
    			           "<td class=\"noborder right\">" + iPercent.toFixed(1) + "%</td>" +
                     "</tr>\n");
        }
      }
      break;
    default:
      // create header
      var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
                  "<thead><tr>" +
                  "<th width=\"1\">&nbsp;</th>" +
                  "<th>" + Lang("Browser") + "</th>" +
                  "<th>" + Lang("Hits") + "</th>" +
    				      "<th class=\"noborder\" width=\"1\">%&nbsp;" + Lang("within Family") + "</th>" +
    				      "<th class=\"noborder\" width=\"1\">%&nbsp;" + Lang("Overall") + "</th>" +
                  "</tr></thead>\n" +
                  "<tbody>";

      // find family totals
      for (var iRow in aFamily) {
        if (aFamily[iRow].sBrowser == sPage) {
          iFamilyTotalHits = aFamily[iRow].iHits;
          break;
        }
      }

      // create output
      for (var iRow in aData) {
        if (aData[iRow].sFamily == sPage) {
          iTotalPercent = ((aData[iRow].iHits / iTotalHits) * 100);
          iFamilyPercent = ((aData[iRow].iHits / iFamilyTotalHits) * 100);
          aHTML.push("<tr>" +
                     "<td class=\"browserlogo\"><img src=\"themes/" + sThemeDir + "/browsers/" + aData[iRow].sFamily.replace(" ", "").replace("-", "").replace("\\", "").toLowerCase() + ".gif\" alt=\"" + aData[iRow].sFamily + "\"/></td>" +
                     "<td>" + aData[iRow].sBrowser + "</td>" +
                     "<td class=\"right\">" + NumberFormat(aData[iRow].iHits, 0) + "</td>" +
  			             "<td class=\"noborder right\">" + iFamilyPercent.toFixed(1) + "%</td>" +
  			             "<td class=\"noborder right\">" + iTotalPercent.toFixed(1) + "%</td>" +
                     "</tr>\n");
        }
      }
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"4\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_Country(sContinent) {
  // get values
  if (typeof sContinent == "undefined") {
    iTotalPages = oStatistics.oCountry.iTotalPages;
    iTotalHits  = oStatistics.oCountry.iTotalHits;
    iTotalBW    = oStatistics.oCountry.iTotalBW;
  } else {
    iTotalPages = oStatistics.oCountry.oContinent[sContinent].iTotalPages;
    iTotalHits  = oStatistics.oCountry.oContinent[sContinent].iTotalHits;
    iTotalBW    = oStatistics.oCountry.oContinent[sContinent].iTotalBW;
  }
  aData       = oStatistics.oCountry.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
		          "<th>&nbsp;</th>" +
              "<th>" + Lang("Country") + "</th>" +
              "<th>" + Lang("Pages") + "</th>" +
              "<th>%</th>" +
              "<th>" + Lang("Hits") + "</th>" +
              "<th>%</th>" +
		          "<th>" + Lang("Bandwidth") + "</th>" +
		          "<th class=\"noborder\">%</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  var aHTML = new Array();
  for (var iRow in aData) {
    if (aData[iRow].sContinent == "Other") {
      aData[iRow].sCountryCode = "trans";
    }
    if ((typeof sContinent == "undefined") || (aData[iRow].sContinent == sContinent)) {
      aHTML.push("<tr>" +
                 "<td class=\"countryflag\"><img src=\"themes/" + sThemeDir + "/flags/" + aData[iRow].sCountryCode + ".gif\" alt=\"" + aData[iRow].sCountryName + "\" /></td>" +
                 "<td>" + Lang(aData[iRow].sCountryName) + "</td>" +
                 "<td class=\"noborder right\">" + NumberFormat(aData[iRow].iPages, 0) + "</td>" +
                 "<td class=\"right\">" + (SafeDivide(aData[iRow].iPages, iTotalPages) * 100).toFixed(1) + "%</td>" +
                 "<td class=\"noborder right\">" + NumberFormat(aData[iRow].iHits, 0) + "</td>" +
                 "<td class=\"right\">" + ((aData[iRow].iHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
                 "<td class=\"noborder right\">" + DisplayBandwidth(aData[iRow].iBW) + "</td>" +
                 "<td class=\"noborder right\">" + ((aData[iRow].iBW / iTotalBW) * 100).toFixed(1) + "%</td>" +
                 "</tr>");
    }
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"8\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_CountryContinent() {
  // get values
  var iTotalPages = oStatistics.oCountry.iTotalPages;
  var iTotalHits  = oStatistics.oCountry.iTotalHits;
  var iTotalBW    = oStatistics.oCountry.iTotalBW;
  var iOtherPages = iTotalPages;
  var iOtherHits  = iTotalHits;
  var iOtherBW    = iTotalBW;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("Continent") + "</th>" +
              "<th>" + Lang("Pages") + "</th>" +
              "<th>%</th>" +
              "<th>" + Lang("Hits") + "</th>" +
              "<th>%</th>" +
		          "<th>" + Lang("Bandwidth") + "</th>" +
		          "<th class=\"noborder\">%</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  for (var sContinent in gc_aContinents) {
    oC = oStatistics.oCountry.oContinent[sContinent];
    iOtherPages -= oC.iTotalPages;
    iOtherHits  -= oC.iTotalHits;
    iOtherBW    -= oC.iTotalBW;
    aHTML.push("<tr>" +
               "<td>" + Lang(sContinent) + " &nbsp;<span class=\"fauxlink tiny\" onclick=\"DrawPage('country." + sContinent + "');\">&raquo;</span></td>" +
               "<td class=\"right\">" + NumberFormat(oC.iTotalPages, 0) + "</td>" +
               "<td class=\"right\">" + ((oC.iTotalPages / iTotalPages) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\">" + NumberFormat(oC.iTotalHits, 0) + "</td>" +
               "<td class=\"right\">" + ((oC.iTotalHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\">" + DisplayBandwidth(oC.iTotalBW) + "</td>" +
               "<td class=\"noborder right\">" + ((oC.iTotalBW / iTotalBW) * 100).toFixed(1) + "%</td>" +
               "</tr>\n");
  }

  // add "other" row
  aHTML.push("<tr>" +
             "<td>" + Lang("Other") + "&nbsp;<span class=\"fauxlink tiny\" onclick=\"DrawPage('country.Other');\">&raquo;</span></td>" +
             "<td class=\"right\">" + NumberFormat(iOtherPages, 0) + "</td>" +
             "<td class=\"right\">" + ((iOtherPages / iTotalPages) * 100).toFixed(1) + "%</td>" +
             "<td class=\"right\">" + NumberFormat(iOtherHits, 0) + "</td>" +
             "<td class=\"right\">" + ((iOtherHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
             "<td class=\"right\">" + DisplayBandwidth(iOtherBW) + "</td>" +
             "<td class=\"noborder right\">" + ((iOtherBW / iTotalBW) * 100).toFixed(1) + "%</td>" +
             "</tr>\n");

  // output
  return (sHTML + aHTML.join("\n") + "</tbody></table>");
}

function DrawTable_Filetypes() {
  // get values
  iTotalHits      = oStatistics.oFiletypes.iTotalHits;
  iTotalBW        = oStatistics.oFiletypes.iTotalBW;
  iTotalNonCompBW = oStatistics.oFiletypes.iTotalNonCompBW;
  iTotalCompBW    = oStatistics.oFiletypes.iTotalCompBW;
  aData           = oStatistics.oFiletypes.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("Filetype") + "</th>" +
              "<th>" + Lang("Description") + "</th>" +
              "<th>" + Lang("Hits") + "</th>" +
              "<th>&nbsp;</th>" +
		          "<th>" + Lang("Bandwidth") + "</th>" +
		          "<th>&nbsp;</th>" +
		          "<th class=\"noborder\">" + Lang("Average Size") + "</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  for (var iRow in aData) {
    aHTML.push("<tr>" +
               "<td>" + oStatistics.oFiletypes.aData[iRow].sFiletype + "</td>" +
               "<td>" + Lang(oStatistics.oFiletypes.aData[iRow].sDescription) + "</td>" +
               "<td class=\"right\">" + NumberFormat(oStatistics.oFiletypes.aData[iRow].iHits, 0) + "</td>" +
               "<td class=\"right\">" + ((oStatistics.oFiletypes.aData[iRow].iHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\">" + DisplayBandwidth(oStatistics.oFiletypes.aData[iRow].iBW) + "</td>" +
               "<td class=\"right\">" + ((oStatistics.oFiletypes.aData[iRow].iBW / iTotalBW) * 100).toFixed(1) + "%</td>" +
               "<td class=\"noborder right\">" + DisplayBandwidth(oStatistics.oFiletypes.aData[iRow].iBW / oStatistics.oFiletypes.aData[iRow].iHits) + "</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"7\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_OperatingSystems(sPage) {
  // get values
  var iTotalHits = oStatistics.oOperatingSystems.iTotalHits;
  var aData      = oStatistics.oOperatingSystems.aData;
  var aFamily    = oStatistics.oOperatingSystems.aFamily;

  // create table body
  var aHTML = [];
  switch (sPage) {
    case "all":
      // create header
      var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
                  "<thead><tr>" +
                  "<th width=\"1\">&nbsp;</th>" +
                  "<th>" + Lang("Operating System") + "</th>" +
                  "<th>" + Lang("Hits") + "</th>" +
    				      "<th class=\"noborder\">&nbsp;</th>" +
                  "</tr></thead>\n" +
                  "<tbody>";

      // create output
      for (var iRow in aData) {
        var iPercent = ((aData[iRow].iHits / iTotalHits) * 100);
        aHTML.push("<tr>" +
                   "<td class=\"oslogo\"><img src=\"themes/" + sThemeDir + "/os/" + aData[iRow].sFamily.replace(" ", "").toLowerCase() + ".gif\" alt=\"" + aData[iRow].sFamily + "\" /></td>" +
                   "<td>" + aData[iRow].sOperatingSystem + "</td>" +
                   "<td class=\"right\">" + NumberFormat(aData[iRow].iHits, 0) + "</td>" +
  			           "<td class=\"noborder right\">" + iPercent.toFixed(1) + "%</td>" +
                   "</tr>\n");
      }
      break;
    case "family":
      // create header
      var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
                  "<thead><tr>" +
                  "<th width=\"1\">&nbsp;</th>" +
                  "<th>" + Lang("Operating System Family") + "</th>" +
                  "<th>" + Lang("Hits") + "</th>" +
    				      "<th class=\"noborder\">&nbsp;</th>" +
                  "</tr></thead>\n" +
                  "<tbody>";

      // create output
      for (var iRow in aFamily) {
        if (aFamily[iRow].iHits > 0) {
          var iPercent = ((aFamily[iRow].iHits / iTotalHits) * 100);
          aHTML.push("<tr>" +
                     "<td class=\"oslogo\"><img src=\"themes/" + sThemeDir + "/os/" + aFamily[iRow].sOperatingSystem.replace(" ", "").toLowerCase() + ".gif\" alt=\"" + aFamily[iRow].sOperatingSystem + "\" /></td>" +
                     "<td>" + gc_aOSFamilyCaption[aFamily[iRow].sOperatingSystem] + " &nbsp;<span class=\"fauxlink tiny\" onclick=\"DrawPage('os." +
                     aFamily[iRow].sOperatingSystem + "');\">&raquo;</span>" + "</td>" +
                     "<td class=\"right\">" + NumberFormat(aFamily[iRow].iHits, 0) + "</td>" +
    			           "<td class=\"noborder right\">" + iPercent.toFixed(1) + "%</td>" +
                     "</tr>\n");
        }
      }
      break;
    default:
      // create header
      var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
                  "<thead><tr>" +
                  "<th width=\"1\">&nbsp;</th>" +
                  "<th>" + Lang("Operating System") + "</th>" +
                  "<th>" + Lang("Hits") + "</th>" +
    				      "<th class=\"noborder\" width=\"1\">%&nbsp;" + (Lang("within Family")).replace(" ", "&nbsp;") + "</th>" +
    				      "<th class=\"noborder\" width=\"1\">%&nbsp;" + Lang("Overall") + "</th>" +
                  "</tr></thead>\n" +
                  "<tbody>";

      // find family totals
      for (var iRow in aFamily) {
        if (aFamily[iRow].sOperatingSystem == sPage) {
          iFamilyTotalHits = aFamily[iRow].iHits;
          break;
        }
      }

      // create output
      for (var iRow in aData) {
        if (aData[iRow].sFamily == sPage) {
          iTotalPercent = ((aData[iRow].iHits / iTotalHits) * 100);
          iFamilyPercent = ((aData[iRow].iHits / iFamilyTotalHits) * 100);
          aHTML.push("<tr>" +
                     "<td class=\"oslogo\"><img src=\"themes/" + sThemeDir + "/os/" + aData[iRow].sFamily.replace(" ", "").toLowerCase() + ".gif\" alt=\"" + aData[iRow].sFamily + "\" /></td>" +
                     "<td>" + aData[iRow].sOperatingSystem + "</td>" +
                     "<td class=\"right\">" + NumberFormat(aData[iRow].iHits, 0) + "</td>" +
  			             "<td class=\"noborder right\">" + iFamilyPercent.toFixed(1) + "%</td>" +
  			             "<td class=\"noborder right\">" + iTotalPercent.toFixed(1) + "%</td>" +
                     "</tr>\n");
        }
      }
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"3\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_PageRefs(sPage) {
  // get values
  iTotalPages = oStatistics.oPageRefs.iTotalPages;
  iTotalHits  = oStatistics.oPageRefs.iTotalHits;
  switch (sPage) {
    case "domains":
      aData       = oStatistics.oPageRefs.aDataDomain;
      break;
    default:
      aData       = oStatistics.oPageRefs.aData;
      break;
  }

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("Referrer") + "</th>" +
              "<th>" + Lang("Pages") + "</th>" +
              "<th>&nbsp;</th>" +
		          "<th>" + Lang("Hits") + "</th>" +
				      "<th class=\"noborder\">&nbsp;</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  for (var iRow in aData) {
    switch (sPage) {
      case "all":
      case "top10":
      case "top50":
        sReferrer = "<a href=\"" + aData[iRow].sURL + "\" target=\"_blank\">" + aData[iRow].sVisibleURL + "</a>";
        break;
      case "domains":
        sReferrer = "<a href=\"" + aData[iRow].sURL + "\" target=\"_blank\">" + aData[iRow].sVisibleURL + "</a>";
        break;
      default:
        sReferrer = aData[iRow].sURL;
    }
    aHTML.push("<tr>" +
               "<td>" + sReferrer + "</td>" +
               "<td class=\"right\">" + NumberFormat(aData[iRow].iPages, 0) + "</td>" +
               "<td class=\"right\">" + (SafeDivide(aData[iRow].iPages, iTotalPages) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\">" + NumberFormat(aData[iRow].iHits, 0) + "</td>" +
               "<td class=\"noborder right\">" + ((aData[iRow].iHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
               "</tr>\n");
    if ((sPage == "top10") && (iRow > 9)) {
      break;
    }
    if ((sPage == "top50") && (iRow > 49)) {
      break;
    }
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"5\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_PageRefsSE(sPage) {
  // get values
  iTotalPages = oStatistics.oPageRefsSE.iTotalPages;
  iTotalHits  = oStatistics.oPageRefsSE.iTotalHits;
  aData       = oStatistics.oPageRefsSE.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th width=\"1\">&nbsp;</th>" +
              "<th>" + Lang("Search Engine") + "</th>" +
              "<th>" + Lang("Pages") + "</th>" +
              "<th>&nbsp;</th>" +
		          "<th>" + Lang("Hits") + "</th>" +
				      "<th class=\"noborder\">&nbsp;</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  for (var iRow in aData) {
    aHTML.push("<tr>" +
               "<td class=\"searchenginelogo\">" + aData[iRow].sImage + "</td>" +
               "<td><!-- " + aData[iRow].sReferrer + " -->" + aData[iRow].sURL + "</td>" +
               "<td class=\"right\">" + NumberFormat(aData[iRow].iPages, 0) + "</td>" +
               "<td class=\"right\">" + (SafeDivide(aData[iRow].iPages, iTotalPages) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\">" + NumberFormat(aData[iRow].iHits, 0) + "</td>" +
               "<td class=\"noborder right\">" + ((aData[iRow].iHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"6\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_Pages(aData) {
  // get values
  var iTotalPages     = oStatistics.oPages.iTotalPages;
  var iTotalBW        = oStatistics.oPages.iTotalBW;
  var iTotalEntry     = oStatistics.oPages.iTotalEntry;
  var iTotalExit      = oStatistics.oPages.iTotalExit;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("URL") + "</th>" +
              "<th>" + Lang("Pages") + "</th>" +
              "<th>&nbsp;</th>" +
		          "<th>" + Lang("Bandwidth") + "</th>" +
		          "<th>&nbsp;</th>" +
		          "<th>" + Lang("Entry") + "</th>" +
		          "<th>&nbsp;</th>" +
				      "<th>" + Lang("Exit") + "</th>" +
				      "<th class=\"noborder\">&nbsp;</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  var aHTML = [];
  for (var iRow in aData) {
    aHTML.push("<tr>" +
               "<td>" + aData[iRow].sURL + "</td>" +
               "<td class=\"right\">" + NumberFormat(aData[iRow].iPages, 0) + "</td>" +
               "<td class=\"right\">" + NumberFormat(SafeDivide(aData[iRow].iPages, iTotalPages) * 100, 1) + "%</td>" +
               "<td class=\"right\">" + DisplayBandwidth(aData[iRow].iBW) + "</td>" +
               "<td class=\"right\">" + NumberFormat(SafeDivide(aData[iRow].iBW, iTotalBW) * 100, 1) + "%</td>" +
               "<td class=\"right\">" + NumberFormat(aData[iRow].iEntry, 0) + "</td>" +
               "<td class=\"right\">" + NumberFormat(SafeDivide(aData[iRow].iEntry, iTotalEntry) * 100, 1) + "%</td>" +
               "<td class=\"right\">" + NumberFormat(aData[iRow].iExit, 0) + "</td>" +
               "<td class=\"noborder right\">" + NumberFormat(SafeDivide(aData[iRow].iExit, iTotalExit) * 100, 1) + "%</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"9\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_Robots() {
  // get values
  iTotalHits      = oStatistics.oRobots.iTotalHits;
  iTotalBW        = oStatistics.oRobots.iTotalBW;
  iTotalRobotsTXT = oStatistics.oRobots.iTotalRobotsTXT;
  aData           = oStatistics.oRobots.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("Spider") + "</th>" +
              "<th>" + Lang("Hits") + "</th>" +
              "<th>&nbsp;</th>" +
		          "<th>" + Lang("Bandwidth") + "</th>" +
		          "<th>&nbsp;</th>" +
		          "<th>" + Lang("Last Visit") + "</th>" +
				      "<th>Robots.txt</th>" +
				      "<th class=\"noborder\">&nbsp;</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  for (var iRow in aData) {
    sDate = oStatistics.oRobots.aData[iRow].dtLastVisit.toString();
    dtDate = new Date(sDate.substr(0,4),
                      (parseInt(StripLeadingZeroes(sDate.substr(4,2))) - 1),
                      sDate.substr(6,2),
                      sDate.substr(8,2),
                      sDate.substr(10,2),
                      sDate.substr(12,2));
    aHTML.push("<tr>" +
               "<td>" + oStatistics.oRobots.aData[iRow].sRobot + "</td>" +
               "<td class=\"right\">" + NumberFormat(oStatistics.oRobots.aData[iRow].iHits, 0) + "</td>" +
               "<td class=\"right\">" + ((oStatistics.oRobots.aData[iRow].iHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\">" + DisplayBandwidth(oStatistics.oRobots.aData[iRow].iBW) + "</td>" +
               "<td class=\"right\">" + ((oStatistics.oRobots.aData[iRow].iBW / iTotalBW) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\"><span class=\"hidden\">" + sDate + "</span>" + dtDate.getDate() + " " + Lang(gc_aMonthName[dtDate.getMonth()].substr(0,3)) + " '" + dtDate.getFullYear().toString().substr(2) + " " + AddLeadingZero(dtDate.getHours(), 2) + ":" + AddLeadingZero(dtDate.getMinutes(), 2) + "</td>" +
               "<td class=\"right\">" + NumberFormat(oStatistics.oRobots.aData[iRow].iRobotsTXT, 0) + "</td>" +
			         "<td class=\"noborder right\">" + (SafeDivide(oStatistics.oRobots.aData[iRow].iRobotsTXT, iTotalRobotsTXT) * 100).toFixed(1) + "%</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"8\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_Session() {
  // get values
  var iTotalFreq = oStatistics.oSession.iTotalFreq;
  var aData      = oStatistics.oSession.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("Session Length") + "</th>" +
              "<th>" + Lang("Frequency") + "</th>" +
				      "<th class=\"noborder\">&nbsp;</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  aHTML.push("<tr>" +
             "<td><!-- 7 -->" + Lang("0 seconds - 30 seconds") + "</td>" +
             "<td>" + NumberFormat(aData.s0s30s, 0) + "</td>" +
             "<td>" + NumberFormat(SafeDivide(aData.s0s30s, iTotalFreq) * 100, 1) + "%</td>" +
             "</tr>\n");
  aHTML.push("<tr>" +
             "<td><!-- 6 -->" + Lang("30 seconds - 2 minutes") + "</td>" +
             "<td>" + NumberFormat(aData.s30s2mn, 0) + "</td>" +
             "<td>" + NumberFormat(SafeDivide(aData.s30s2mn, iTotalFreq) * 100, 1) + "%</td>" +
             "</tr>\n");
  aHTML.push("<tr>" +
             "<td><!-- 5 -->" + Lang("2 minutes - 5 minutes") + "</td>" +
             "<td>" + NumberFormat(aData.s2mn5mn, 0) + "</td>" +
             "<td>" + NumberFormat(SafeDivide(aData.s2mn5mn, iTotalFreq) * 100, 1) + "%</td>" +
             "</tr>\n");
  aHTML.push("<tr>" +
             "<td><!-- 4 -->" + Lang("5 minutes - 15 minutes") + "</td>" +
             "<td>" + NumberFormat(aData.s5mn15mn, 0) + "</td>" +
             "<td>" + NumberFormat(SafeDivide(aData.s5mn15mn, iTotalFreq) * 100, 1) + "%</td>" +
             "</tr>\n");
  aHTML.push("<tr>" +
             "<td><!-- 3 -->" + Lang("15 minutes - 30 minutes") + "</td>" +
             "<td>" + NumberFormat(aData.s15mn30mn, 0) + "</td>" +
             "<td>" + NumberFormat(SafeDivide(aData.s15mn30mn, iTotalFreq) * 100, 1) + "%</td>" +
             "</tr>\n");
  aHTML.push("<tr>" +
             "<td><!-- 2 -->" + Lang("30 minutes - 1 hour") + "</td>" +
             "<td>" + NumberFormat(aData.s30mn1h, 0) + "</td>" +
             "<td>" + NumberFormat(SafeDivide(aData.s30mn1h, iTotalFreq) * 100, 1) + "%</td>" +
             "</tr>\n");
  aHTML.push("<tr>" +
             "<td><!-- 1 -->" + Lang("More than 1 hour") + "</td>" +
             "<td>" + NumberFormat(aData.s1h, 0) + "</td>" +
             "<td>" + NumberFormat(SafeDivide(aData.s1h, iTotalFreq) * 100, 1) + "%</td>" +
             "</tr>\n");

  // output
  return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
}

function DrawTable_Status() {
  // get values
  iTotalHits      = oStatistics.oStatus.iTotalHits;
  iTotalBW        = oStatistics.oStatus.iTotalBW;
  aData           = oStatistics.oStatus.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("Code") + "</th>" +
              "<th>" + Lang("Description") + "</th>" +
              "<th>" + Lang("Hits") + "</th>" +
              "<th>&nbsp;</th>" +
		          "<th>" + Lang("Bandwidth") + "</th>" +
		          "<th>&nbsp;</th>" +
		          "<th class=\"noborder\">" + Lang("Average Size") + "</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  for (var iRow in aData) {
    aHTML.push("<tr>" +
               "<td>" + oStatistics.oStatus.aData[iRow].sCode + "</td>" +
               "<td>" + Lang(oStatistics.oStatus.aData[iRow].sDescription) + "</td>" +
               "<td class=\"right\">" + NumberFormat(oStatistics.oStatus.aData[iRow].iHits, 0) + "</td>" +
               "<td class=\"right\">" + ((oStatistics.oStatus.aData[iRow].iHits / iTotalHits) * 100).toFixed(1) + "%</td>" +
               "<td class=\"right\">" + DisplayBandwidth(oStatistics.oStatus.aData[iRow].iBW) + "</td>" +
               "<td class=\"right\">" + ((oStatistics.oStatus.aData[iRow].iBW / iTotalBW) * 100).toFixed(1) + "%</td>" +
               "<td class=\"noborder right\">" + DisplayBandwidth(oStatistics.oStatus.aData[iRow].iBW / oStatistics.oStatus.aData[iRow].iHits) + "</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"7\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_Status404() {
  // get values
  iTotalHits      = oStatistics.oStatus404.iTotalHits;
  aData           = oStatistics.oStatus404.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("URL") + "</th>" +
              "<th>" + Lang("Hits") + "</th>" +
              "<th>" + Lang("Referrer") + "</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  var sReferrer = "";
  for (var iRow in aData) {
    if (oStatistics.oStatus404.aData[iRow].sReferrer == "-") {
      sReferrer = "&nbsp;";
    } else {
      sReferrer = ("<a href=\"" + oStatistics.oStatus404.aData[iRow].sReferrer + "\" target=\"_blank\">" + oStatistics.oStatus404.aData[iRow].sReferrerVisible + "</a>");
    }
    aHTML.push("<tr>" +
               "<td>" + oStatistics.oStatus404.aData[iRow].sURL + "</td>" +
               "<td class=\"right\">" + NumberFormat(oStatistics.oStatus404.aData[iRow].iHits, 0) + "</td>" +
               "<td>" + sReferrer + "</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    return ( [ true, (sHTML + aHTML.join("\n") + "</tbody></table>") ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"3\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_ThisMonth() {
  // get values
  iTotalVisits    = oStatistics.oThisMonth.iTotalVisits;
  iTotalPages     = oStatistics.oThisMonth.iTotalPages;
  iTotalHits      = oStatistics.oThisMonth.iTotalHits;
  iTotalBW        = oStatistics.oThisMonth.iTotalBW;
  aData           = oStatistics.oThisMonth.aData;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th width=\"15%\">" + Lang("Day") + "</th>" +
              "<th width=\"14%\">" + Lang("Date") + "</th>" +
              "<th width=\"9%\">" + Lang("Visits") + "</th>" +
              "<th width=\"9%\" class=\"noborder\">" + Lang("Pages") + "</th>" +
              "<th width=\"11%\">" + Lang("per Visit") + "</th>" +
              "<th width=\"9%\" class=\"noborder\">" + Lang("Hits") + "</th>" +
              "<th width=\"11%\">" + Lang("per Visit") + "</th>" +
              "<th width=\"9%\" class=\"noborder\">" + Lang("BW") + "</th>" +
			        "<th width=\"11%\" class=\"noborder\">" + Lang("per Visit") + "</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  for (var iRow in aData) {
    oRow = oStatistics.oThisMonth.aData[iRow];
	  sVisibleDate = (oRow.dtDate.getDate() + " " +
	                  Lang(gc_aMonthName[oRow.dtDate.getMonth()].substr(0,3)) + " '" +
	                  oRow.dtDate.getFullYear().toString().substr(2));
	  if (oRow.dtDate.getDay() == 0) {
	    sRowStyle = " class=\"sunday\"";
	  } else if (oRow.dtDate.getDay() == 6) {
	    sRowStyle = " class=\"saturday\"";
	  } else {
	    sRowStyle = "";
	  }
    aHTML.push("<tr" + sRowStyle + ">" +
               "<td><span class=\"hidden\">" + oRow.dtDate.getDay() + "</span>" + Lang(gc_aDayName[oRow.dtDate.getDay()]) + "</td>" +
               "<td><span class=\"hidden\">" + oRow.dtDate.valueOf() + "</span>" + sVisibleDate + "</td>" +
               "<td class=\"right\">" + NumberFormat(oRow.iVisits, 0) + "</td>" +
               "<td class=\"right\">" + NumberFormat(oRow.iPages, 0) + "</td>" +
               "<td class=\"right\">" + NumberFormat((SafeDivide(oRow.iPages, oRow.iVisits)), 1) + "</td>" +
               "<td class=\"right\">" + NumberFormat(oRow.iHits, 0) + "</td>" +
      			   "<td class=\"right\">" + NumberFormat(SafeDivide(oRow.iHits, oRow.iVisits), 1) + "</td>" +
      			   "<td class=\"right\">" + DisplayBandwidth(oRow.iBW) + "</td>" +
      			   "<td class=\"noborder right\">" + DisplayBandwidth(SafeDivide(oRow.iBW, oRow.iVisits)) + "</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    sHTML = (sHTML + aHTML.join("\n") + "</tbody><tfoot><tr>" +
             "<td colspan=\"3\" class=\"noborder right\">" + NumberFormat(iTotalVisits, 0) + "</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalPages, 0) + "</td>" +
             "<td class=\"noborder right\">" + NumberFormat(SafeDivide(iTotalPages, iTotalVisits), 1) + "</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalHits) + "</td>" +
             "<td class=\"noborder right\">" + NumberFormat(SafeDivide(iTotalHits, iTotalVisits), 2) + "</td>" +
             "<td class=\"noborder right\">" + DisplayBandwidth(iTotalBW) + "</td>" +
             "<td class=\"noborder right\">" + DisplayBandwidth(SafeDivide(iTotalBW, iTotalVisits)) + "</td>" +
             "</tr></tfoot></table>")
    return ( [ true, sHTML ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"10\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }
}

function DrawTable_Time() {
  // get values
  var iTotalPages     = oStatistics.oTime.iTotalPages;
  var iTotalHits      = oStatistics.oTime.iTotalHits;
  var iTotalBW        = oStatistics.oTime.iTotalBW;
  var iTotalNVPages   = oStatistics.oTime.iTotalNVPages;
  var iTotalNVHits    = oStatistics.oTime.iTotalNVHits;
  var iTotalNVBW      = oStatistics.oTime.iTotalNVBW;

  // create header
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th>" + Lang("Hour") + "</th>" +
              "<th class=\"noborder\">" + Lang("Pages") + "</th>" +
              "<th class=\"noborder right\">%</th>" +
              "<th class=\"right\">+/-</th>" +
              "<th class=\"noborder\">" + Lang("Hits") + "</th>" +
              "<th class=\"noborder right\">%</th>" +
              "<th class=\"right\">+/-</th>" +
              "<th class=\"noborder\">" + Lang("BW") + "</th>" +
              "<th class=\"noborder right\">%</th>" +
              "<th class=\"right\">+/-</th>" +
              "<th width=\"1\"><small>" + (Lang("Not Viewed")).replace(" ", "&nbsp;") + "</small><br />" + Lang("Pages") + "</th>" +
              "<th width=\"1\"><small>" + (Lang("Not Viewed")).replace(" ", "&nbsp;") + "</small><br />" + Lang("Hits") + "</th>" +
              "<th class=\"noborder\" width=\"1\"><small>" + (Lang("Not Viewed")).replace(" ", "&nbsp;") + "</small><br />" + Lang("BW") + "</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  var aHTML     = [];
  var iAvgPages = (iTotalPages / 24);
  var iAvgHits  = (iTotalHits / 24);
  var iAvgBW    = (iTotalBW / 24);
  for (var iRow in oStatistics.oTime.aData) {
    var oRow = oStatistics.oTime.aData[iRow];
    var sHour = oRow.iHour;
    if (oRow.iHour < 10) {
      sHour = ("0" + sHour)
    }

    // +/- values
    var sPagesDiff = Difference(oRow.iPages, iAvgPages);
    var sHitsDiff = Difference(oRow.iHits, iAvgHits);
    var sBWDiff = Difference(oRow.iBW, iAvgBW);

    // create table
    aHTML.push("<tr>" +
               "<td>" + sHour + "</td>" +
               "<td class=\"right\">" + NumberFormat(oRow.iPages, 0) + "</td>" +
               "<td class=\"right\">" + NumberFormat((SafeDivide(oRow.iPages, iTotalPages) * 100), 1) + "%</td>" +
               "<td class=\"right\">" + sPagesDiff + "</td>" +
               "<td class=\"right\">" + NumberFormat(oRow.iHits, 0) + "</td>" +
               "<td class=\"right\">" + NumberFormat((SafeDivide(oRow.iHits, iTotalHits) * 100), 1) + "%</td>" +
               "<td class=\"right\">" + sHitsDiff + "</td>" +
               "<td class=\"right\">" + DisplayBandwidth(oRow.iBW) + "</td>" +
               "<td class=\"right\">" + NumberFormat((SafeDivide(oRow.iBW, iTotalBW) * 100), 1) + "%</td>" +
               "<td class=\"right\">" + sBWDiff + "</td>" +
               "<td class=\"right\">" + NumberFormat(oRow.iNVPages, 0) + "</td>" +
               "<td class=\"right\">" + NumberFormat(oRow.iNVHits, 0) + "</td>" +
               "<td class=\"noborder right\">" + DisplayBandwidth(oRow.iNVBW) + "</td>" +
               "</tr>\n");
  }

  // output
  if (aHTML.length > 0) {
    sHTML = (sHTML + aHTML.join("\n") + "</tbody><tfoot><tr>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalPages, 0) + "</td>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalHits, 0) + "</td>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder right\">" + DisplayBandwidth(iTotalBW) + "</td>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder\">&nbsp;</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalNVPages, 0) + "</td>" +
             "<td class=\"noborder right\">" + NumberFormat(iTotalNVHits, 0) + "</td>" +
             "<td class=\"noborder right\">" + DisplayBandwidth(iTotalNVBW) + "</td>" +
             "</tr></tfoot></table>")
    return ( [ true, sHTML ] );
  } else {
    return ( [ false, (sHTML + "<tr><td class=\"center\" colspan=\"4\">" + Lang("There is no data to display") + "</td></tr></tbody></table>") ] );
  }

  function Difference(iValue, iAverage) {
    if (iValue == iAverage) {
      return "-";
    } else {
      if (iValue > iAverage) {
        return ("<span class=\"tiny positive\">+" + NumberFormat((SafeDivide((iValue - iAverage), iAverage) * 100), 1) + "%</span>");
      } else {
        return ("<span class=\"tiny negative\">-" + NumberFormat((SafeDivide((iAverage - iValue), iAverage) * 100), 1) + "%</span>");
      }
    }
  }
}

function Lang(sPhrase) {
  return (oTranslation[sPhrase] || sPhrase);
}

function Misc_ThisMonthCalendar(sHeadline, sSubMenu, sDataItem) {
  // create sum arrays
  var aWeek = [];
  var aDay = [];
  for (var iIndex = 0; iIndex < 7; iIndex++) {
    aDay[iIndex] = { iCount:0, iTotal:0 };
  }
  var iTotal = 0;

  // calculate dates
  var iFirstWeek = getWeekNr(oStatistics.oThisMonth.aData[0].dtDate);
  var dtLastDayOfMonth = new Date(oStatistics.oThisMonth.aData[0].dtDate.getFullYear(),
                                  (oStatistics.oThisMonth.aData[0].dtDate.getMonth() + 1),
                                  0);
  var iLastWeek = getWeekNr(dtLastDayOfMonth);

  // create table
  var sHTML = "<table class=\"calendar\"><tbody>" +
              "<tr>" +
              "<td class=\"labelTop\">&nbsp;</td>" +
              "<td class=\"labelTop\">" + Lang("Monday") + "</td>" +
              "<td class=\"labelTop\">" + Lang("Tuesday") + "</td>" +
              "<td class=\"labelTop\">" + Lang("Wednesday") + "</td>" +
              "<td class=\"labelTop\">" + Lang("Thursday") + "</td>" +
              "<td class=\"labelTop\">" + Lang("Friday") + "</td>" +
              "<td class=\"labelTop\">" + Lang("Saturday") + "</td>" +
              "<td class=\"labelTop\">" + Lang("Sunday") + "</td>" +
              "<td class=\"labelTopSpacer\">&nbsp;</td>" +
              "<td class=\"labelTop\">" + Lang("Week Total") + "</td>" +
              "<td class=\"labelTop\">" + Lang("Daily Average") + "</td>" +
              "</tr>";
  for (var iIndex = iFirstWeek; iIndex <= iLastWeek; iIndex++) {
    aWeek[iIndex] = { iCount:0, iTotal:0 };
    sHTML += "<tr>" +
             "<td id=\"calWeek" + iIndex + "\" class=\"labelSide\">" + Lang("Week") + ":&nbsp;" + iIndex + "</td>" +
             "<td id=\"calDay1-" + iIndex + "\">&nbsp;</td>" +
             "<td id=\"calDay2-" + iIndex + "\">&nbsp;</td>" +
             "<td id=\"calDay3-" + iIndex + "\">&nbsp;</td>" +
             "<td id=\"calDay4-" + iIndex + "\">&nbsp;</td>" +
             "<td id=\"calDay5-" + iIndex + "\">&nbsp;</td>" +
             "<td id=\"calDay6-" + iIndex + "\">&nbsp;</td>" +
             "<td id=\"calDay0-" + iIndex + "\">&nbsp;</td>" +
             "<td>&nbsp;</td>" +
             "<td id=\"calTotWk" + iIndex + "\" class=\"calTotWk\">&nbsp;</td>" +
             "<td id=\"calAvgWk" + iIndex + "\" class=\"calAvgWk\">&nbsp;</td>" +
             "</tr>";
  }
  sHTML += "<tr>" +
           "<td>&nbsp;</td>" +
           "<td colspan=\"7\" id=\"graph\" class=\"calGraph\">&nbsp;</td>" +
           "<td colspan=\"3\">&nbsp;</td>" +
           "</tr><tr>" +
           "<td class=\"labelSide\">" + Lang("Day of Week Total") + "</td>" +
           "<td id=\"calTotDay1\" class=\"calTotDay\">&nbsp;</td>" +
           "<td id=\"calTotDay2\" class=\"calTotDay\">&nbsp;</td>" +
           "<td id=\"calTotDay3\" class=\"calTotDay\">&nbsp;</td>" +
           "<td id=\"calTotDay4\" class=\"calTotDay\">&nbsp;</td>" +
           "<td id=\"calTotDay5\" class=\"calTotDay\">&nbsp;</td>" +
           "<td id=\"calTotDay6\" class=\"calTotDay\">&nbsp;</td>" +
           "<td id=\"calTotDay0\" class=\"calTotDay\">&nbsp;</td>" +
           "<td>&nbsp;</td>" +
           "<td colspan=\"2\" id=\"calTotMonth\" class=\"calTotDay\">&nbsp;</td>" +
           "</tr><tr>" +
           "<td class=\"labelSide\">" + Lang("Day of Week Average") + "</td>" +
           "<td id=\"calAvgDay1\" class=\"calAvgDay\">&nbsp;</td>" +
           "<td id=\"calAvgDay2\" class=\"calAvgDay\">&nbsp;</td>" +
           "<td id=\"calAvgDay3\" class=\"calAvgDay\">&nbsp;</td>" +
           "<td id=\"calAvgDay4\" class=\"calAvgDay\">&nbsp;</td>" +
           "<td id=\"calAvgDay5\" class=\"calAvgDay\">&nbsp;</td>" +
           "<td id=\"calAvgDay6\" class=\"calAvgDay\">&nbsp;</td>" +
           "<td id=\"calAvgDay0\" class=\"calAvgDay\">&nbsp;</td>" +
           "<td>&nbsp;</td>" +
           "<td colspan=\"2\" id=\"calAvgMonth\" class=\"calAvgDay\">&nbsp;</td>" +
           "</tr>";
  sHTML += "</tbody></table>";

  // apply content
  $("#content").html("<h2>" + Lang(sHeadline) + "</h2>" +
                     DrawSubMenu("thismonth", sSubMenu) +
                     "<div class=\"tableFull\">" + sHTML + "</div>");

  // populate daily values
  for (var iRow in oStatistics.oThisMonth.aData) {
    var oRow = oStatistics.oThisMonth.aData[iRow];
    var iWeekNumber = getWeekNr(oRow.dtDate);
    var iDayNumber = oRow.dtDate.getDay();

    // increment counters
    aWeek[iWeekNumber].iCount++;
    aWeek[iWeekNumber].iTotal += oRow[sDataItem];
    aDay[iDayNumber].iCount++;
    aDay[iDayNumber].iTotal += oRow[sDataItem];
    iTotal += oRow[sDataItem];

    // modify table
    if (sDataItem == "iBW") {
      sHTML = ("<div class=\"date\">" + oRow.dtDate.getDate() + "</div><div class=\"value\">" + DisplayBandwidth(oRow[sDataItem]) + "</div>");
    } else {
      sHTML = ("<div class=\"date\">" + oRow.dtDate.getDate() + "</div><div class=\"value\">" + NumberFormat(oRow[sDataItem], 0) + "</div>");
    }
    $("#calDay" + iDayNumber + "-" + iWeekNumber).html(sHTML).addClass("calDayPopulated");
  }

  // populate week totals
  for (var iIndex = iFirstWeek; iIndex <= iLastWeek; iIndex++) {
    if (aWeek[iIndex].iCount > 0) {
      if (sDataItem == "iBW") {
        $("#calTotWk" + iIndex).html("<div>" + DisplayBandwidth(aWeek[iIndex].iTotal) + "</div>");
        $("#calAvgWk" + iIndex).html("<div>" + DisplayBandwidth(aWeek[iIndex].iTotal / aWeek[iIndex].iCount) + "</div>");
      } else {
        $("#calTotWk" + iIndex).html("<div>" + NumberFormat(aWeek[iIndex].iTotal, 0) + "</div>");
        $("#calAvgWk" + iIndex).html("<div>" + NumberFormat((aWeek[iIndex].iTotal / aWeek[iIndex].iCount), 1) + "</div>");
      }
    }
  }

  // populate day totals
  for (var iIndex = 0; iIndex < 7; iIndex++) {
    if (aDay[iIndex].iCount > 0) {
      if (sDataItem == "iBW") {
        $("#calTotDay" + iIndex).html("<div>" + DisplayBandwidth(aDay[iIndex].iTotal) + "</div>");
        $("#calAvgDay" + iIndex).html("<div>" + DisplayBandwidth(aDay[iIndex].iTotal / aDay[iIndex].iCount) + "</div>");
      } else {
        $("#calTotDay" + iIndex).html("<div>" + NumberFormat(aDay[iIndex].iTotal, 0) + "</div>");
        $("#calAvgDay" + iIndex).html("<div>" + NumberFormat((aDay[iIndex].iTotal / aDay[iIndex].iCount), 1) + "</div>");
      }
    }
  }

  // fill in any remaining empty days
  var dtThisDate = new Date(oRow.dtDate.getFullYear(), oRow.dtDate.getMonth(), (oRow.dtDate.getDate() + 1));
  while (dtThisDate.getMonth() == dtLastDayOfMonth.getMonth()) {
    $("#calDay" + dtThisDate.getDay() + "-" + getWeekNr(dtThisDate)).html("<div class=\"date\">" + dtThisDate.getDate() + "</div>").addClass("calDay");
    dtThisDate.setDate(dtThisDate.getDate() + 1);
  }

  // populate month totals
  if (sDataItem == "iBW") {
    $("#calTotMonth").html("<div><span>" + Lang("Total") + ":</span> " + DisplayBandwidth(iTotal) + "</div>");
    $("#calAvgMonth").html("<div><span>" + Lang("Average") + ":</span> " + DisplayBandwidth(iTotal / oRow.dtDate.getDate()) + "</div>");
  } else {
    $("#calTotMonth").html("<div><span>" + Lang("Total") + ":</span> " + NumberFormat(iTotal, 0) + "</div>");
    $("#calAvgMonth").html("<div><span>" + Lang("Average") + ":</span> " + NumberFormat((iTotal / oRow.dtDate.getDate()), 1) + "</div>");
  }

  // draw graph
  var aGraphItem = [Lang("Monday"), Lang("Tuesday"), Lang("Wednesday"), Lang("Thursday"), Lang("Friday"), Lang("Saturday"), Lang("Sunday")];
  var aGraphValue = [SafeDivide(aDay[1].iTotal, aDay[1].iCount),
                     SafeDivide(aDay[2].iTotal, aDay[2].iCount),
                     SafeDivide(aDay[3].iTotal, aDay[3].iCount),
                     SafeDivide(aDay[4].iTotal, aDay[4].iCount),
                     SafeDivide(aDay[5].iTotal, aDay[5].iCount),
                     SafeDivide(aDay[6].iTotal, aDay[6].iCount),
                     SafeDivide(aDay[0].iTotal, aDay[0].iCount)];
  DrawGraph(["","","","","","",""], aGraphValue, aGraphItem, "bar");
}

function PageLayout_AllMonths(sPage) {
  var aTable = DrawTable_AllMonths(sPage);
  switch (sPage) {
    case "all":
      var sHTML = "<h2>" + Lang("Visitors each Month") + "</h2>" +
                  DrawSubMenu("allmonths", "Visitors each Month") +
                  "<div id=\"graph\" class=\"graph\">&nbsp;</div>";
      break;
    case "year":
      var sHTML = "<h2>" + Lang("Visitors each Year") + "</h2>" +
                  DrawSubMenu("allmonths", "Visitors each Year");
      break;
  }
  sHTML += "<div class=\"tableFull\">" + aTable[1] + "</div>";
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers:{1:{sorter:"commaNumber"},2:{sorter:"commaNumber"},3:{sorter:"commaNumber"},5:{sorter:"commaNumber"},6:{sorter:"commaNumber"},7:{sorter:'bandwidth'}},sortList: [[0,0]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  if (sPage == "all") {
    DrawGraph_AllMonths();
  }
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Browser(sPage) {
  var aTable = DrawTable_Browser(sPage);
  switch (sPage) {
    case "family":
      var sHTML = "<h2>" + Lang("Browser Families") + "</h2>" +
                  DrawSubMenu("browser", "Browser Families") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
    case "all":
      var sHTML = "<h2>" + Lang("All Browsers") + "</h2>" +
                  DrawSubMenu("browser", "All Browsers") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
    default:
      var sHTML = "<h2>" + Lang("Browser Family") + ": " + gc_aBrowserFamilyCaption[sPage] + "</h2>" +
                  DrawSubMenu("browser", "") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
  }
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers: { 0: { sorter: false }, 2:{sorter:"commaNumber"}, 3: { sorter: false } }, sortList: [[2,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_Browser(sPage);
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Country(sPage) {
  switch (sPage) {
    case "all":
      var aTable = DrawTable_Country();
      var sHTML = "<h2>" + Lang("Visitors by Country") + "</h2>" +
                  DrawSubMenu("country", "Countries") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" +
                  aTable[1] +
                  "</div>";
      $("#content").html(sHTML);
      if (aTable[0] == true) {
        $(".tablesorter").tablesorter( { headers: { 0:{sorter:false},2:{sorter:"commaNumber"},3:{sorter:false},4:{sorter:"commaNumber"},5:{sorter:false},6:{sorter:'bandwidth'},7:{sorter:false} }, sortList: [[2,1]], textExtraction:function(node){return node.innerHTML.replace(',','');}, widgets: ['zebra'] } );
      }
      DrawPie_Country();
      break;
    case "continent":
      var sHTML = "<h2>" + Lang("Visitors by Continent") + "</h2>" +
                  DrawSubMenu("country", "Continents") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" +
                  DrawTable_CountryContinent() +
                  "</div>";
      $("#content").html(sHTML);
      $(".tablesorter").tablesorter( { headers: { 1:{sorter:"commaNumber"}, 2: { sorter: false }, 3:{sorter:"commaNumber"}, 4: { sorter: false },5:{sorter:'bandwidth'}, 6: { sorter: false } }, sortList: [[1,1]], textExtraction: function(node) { return node.innerHTML.replace(',', '');}, widgets: ['zebra'] } );
      DrawPie_CountryContinent();
      break;
    default:
      if (sPage == "Other") {
        var sHTML = "<h2>" + Lang("Other Visitors") + "</h2>";
      } else {
        var sHTML = "<h2>" + Lang("Visitors from " + sPage) + "</h2>";
      }
      var aTable = DrawTable_Country(sPage);
      sHTML += DrawSubMenu("country", sPage) +
               "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" +
               aTable[1] + "</div>";
      $("#content").html(sHTML);
      if (aTable[0] == true) {
        $(".tablesorter").tablesorter( { headers: { 0:{sorter:false},2:{sorter:"commaNumber"},3:{sorter:false},4:{sorter:"commaNumber"},5:{sorter:false},6:{sorter:'bandwidth'},7:{sorter:false } }, sortList: [[2,1]], textExtraction: function(node) { return node.innerHTML.replace(',', '');}, widgets: ['zebra'] } );
      }
      DrawPie_Country(sPage);
      break;
  }
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Filetypes() {
  var aTable = DrawTable_Filetypes();
  var sHTML = "<h2>" + Lang("Filetypes") + "</h2><div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers: { 2:{sorter:"commaNumber"}, 3: { sorter: false }, 4:{sorter:'bandwidth'}, 5: { sorter: false }, 6:{sorter:'bandwidth'} }, sortList: [[2,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_Filetypes();
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_OperatingSystems(sPage) {
  var aTable = DrawTable_OperatingSystems(sPage);
  switch (sPage) {
    case "family":
      var sHTML = "<h2>" + Lang("Operating System Families") + "</h2>" +
                  DrawSubMenu("os", "Operating System Families") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
    case "all":
      var sHTML = "<h2>" + Lang("Operating Systems") + "</h2>" +
                  DrawSubMenu("os", "All Operating Systems") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
    default:
      var sHTML = "<h2>" + Lang("Operating System Family") + ": " + gc_aOSFamilyCaption[sPage] + "</h2>" +
                  DrawSubMenu("os", "") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
  }
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers: { 0: { sorter: false }, 2: { sorter: "commaNumber" }, 3: { sorter: false }, 4: { sorter: false } }, sortList: [[2,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_OperatingSystems(sPage);
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_PageRefs(sPage) {
  switch (sPage) {
    case "all":
      var aTable = DrawTable_PageRefs("all");
      var sHTML = "<h2>" + Lang("Referring Pages") + "</h2>" +
                  DrawSubMenu("pagerefs", "All Referrers") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
    case "domains":
      var aTable = DrawTable_PageRefs("domains");
      var sHTML = "<h2>" + Lang("Referring Domains") + "</h2>" +
                  DrawSubMenu("pagerefs", "Referring Domains") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
    case "top10":
      var aTable = DrawTable_PageRefs("top10");
      var sHTML = "<h2>" + Lang("Referring Pages") + "</h2>" +
                  DrawSubMenu("pagerefs", "Top 10 Referrers") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
    case "top50":
      var aTable = DrawTable_PageRefs("top50");
      var sHTML = "<h2>" + Lang("Referring Pages") + "</h2>" +
                  DrawSubMenu("pagerefs", "Top 50 Referrers") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      break;
  }
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers: { 1:{sorter:"commaNumber"}, 2: { sorter: false }, 3:{sorter:"commaNumber"}, 4: { sorter: false } }, sortList: [[1,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_PageRefs(sPage);
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_PageRefsSE() {
  var aTable = DrawTable_PageRefsSE();
  var sHTML = "<h2>" + Lang("Referring Search Engines") + "</h2>" +
              DrawSubMenu("pagerefs", "Search Engines") +
              "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers: { 0: { sorter: false }, 2:{sorter:"commaNumber"}, 3: { sorter: false }, 4:{sorter:"commaNumber"}, 5: { sorter: false } }, sortList: [[2,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_PageRefsSE();
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Pages(sPage) {
  // select data
  switch (sPage) {
    case "topBW":
      var aData = oStatistics.oPages.aDataBW;
      var aSort = [3,1];
      var sSubMenu = "Top Bandwidth";
      var iPieTotal = oStatistics.oPages.iTotalBW;
      var sPieItem = "iBW";
      break;
    case "topEntry":
      var aData = oStatistics.oPages.aDataEntry;
      var aSort = [5,1];
      var sSubMenu = "Top Entry Pages";
      var iPieTotal = oStatistics.oPages.iTotalEntry;
      var sPieItem = "iEntry";
      break;
    case "topExit":
      var aData = oStatistics.oPages.aDataExit;
      var aSort = [7,1];
      var sSubMenu = "Top Exit Pages";
      var iPieTotal = oStatistics.oPages.iTotalExit;
      var sPieItem = "iExit";
      break;
    case "topPages":
      var aData = oStatistics.oPages.aDataPages;
      var aSort = [1,1];
      var sSubMenu = "Top Page Views";
      var iPieTotal = oStatistics.oPages.iTotalPages;
      var sPieItem = "iPages";
      break;
  }

  // create html
  var aTable = DrawTable_Pages(aData);
  var sHTML = "<h2>" + Lang("Page Views") + "</h2>" +
              DrawSubMenu("pages", sSubMenu) +
              "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers: { 2: { sorter: false }, 3:{sorter:'bandwidth'}, 4: { sorter: false }, 6: { sorter: false }, 8: { sorter: false } }, sortList: [aSort],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_Pages(aData, iPieTotal, sPieItem);
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Robots() {
  var aTable = DrawTable_Robots();
  var sHTML = "<h2>" + Lang("Visiting Spiders") + "</h2><div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers: { 1:{sorter:"commaNumber"}, 2: { sorter: false }, 3:{sorter:'bandwidth'}, 4: { sorter: false }, 6:{sorter:"commaNumber"}, 7: { sorter: false } }, sortList: [[1,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_Robots();
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Searches(sPage) {
  switch (sPage) {
    case "keyphrasecloud":
      var sHTML = "<h2>" + Lang("Keyphrases Tag Cloud") + "</h2>" +
                  DrawSubMenu("searches", "Keyphrases Tag Cloud") +
                  "<div class=\"tagcloud\">" + TagCloud("sPhrase", oStatistics.oKeyphrases, 75) + "</div>";
      $("#content").html(sHTML);
      break;
    case "keyphrases":
      var sHTML = "<h2>" + Lang("Keyphrases") + "</h2>" +
                  DrawSubMenu("searches", "Keyphrases") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + Paging_Keyphrases() + "</div>";
      $("#content").html(sHTML);
      DrawPie_Keyphrases();
      break;
    case "keywordcloud":
      var sHTML = "<h2>" + Lang("Keywords Tag Cloud") + "</h2>" +
                  DrawSubMenu("searches", "Keywords Tag Cloud") +
                  "<div class=\"tagcloud\">" + TagCloud("sWord", oStatistics.oKeywords, 150) + "</div>";
      $("#content").html(sHTML);
      break;
    case "keywords":
      var sHTML = "<h2>" + Lang("Keywords") + "</h2>" +
                  DrawSubMenu("searches", "Keywords") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + Paging_Keywords() + "</div>";
      $("#content").html(sHTML);
      DrawPie_Keywords();
      break;
  }
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Session() {
  var aTable = DrawTable_Session();
  var sHTML = "<h2>" + Lang("Session Duration") + "</h2><div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    //$(".tablesorter").tablesorter({ headers: { 0: { sorter: false }, 1:{sorter:"commaNumber"}, 2: { sorter: false } }, sortList: [[1,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
    $(".tablesorter").tablesorter({ headers: { 1:{sorter:"commaNumber"}, 2: { sorter: false } }, sortList: [[0,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawPie_Session();
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Status(sPage) {
  switch (sPage) {
    case "404":
      var aTable = DrawTable_Status404();
      var sHTML = "<h2>" + Lang("HTTP Status Codes") + ": 404s</h2>" +
                  DrawSubMenu("status", "File Not Found URLs") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      $("#content").html(sHTML);
      if (aTable[0] == true) {
        $(".tablesorter").tablesorter({ headers: { 1: { sorter: "commaNumber" } }, sortList: [[1,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
      }
      DrawPie_Status404(sPage);
      break;
    default:
      var aTable = DrawTable_Status();
      var sHTML = "<h2>" + Lang("HTTP Status Codes") + "</h2>" +
                  DrawSubMenu("status", "Status Codes") +
                  "<div id=\"pie\" class=\"pie\">&nbsp;</div><div class=\"tablePie\">" + aTable[1] + "</div>";
      $("#content").html(sHTML);
      if (aTable[0] == true) {
        $(".tablesorter").tablesorter({ headers: { 2: { sorter: "commaNumber" }, 3:{ sorter: false }, 4: { sorter: "bandwidth" }, 5: { sorter: false } }, sortList: [[2,1]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
      }
      DrawPie_Status(sPage);
  }
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_ThisMonth(sPage) {
  switch (sPage) {
    case "all":
      var aTable = DrawTable_ThisMonth();
      var sHTML = "<h2>" + Lang("Visitors this Month") + "</h2>" +
                  DrawSubMenu("thismonth", "Overview") +
                  "<div id=\"graph\" class=\"graph\">&nbsp;</div><div class=\"tableFull\">" + aTable[1] + "</div>";
      $("#content").html(sHTML);
      if (aTable[0] == true) {
        $(".tablesorter").tablesorter({ headers:{ 2:{sorter:"commaNumber"},3:{sorter:"commaNumber"},5:{sorter:"commaNumber"},7:{sorter:"bandwidth"},8:{sorter:"bandwidth"}},sortList:[[1,0]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
      }
      DrawGraph_ThisMonth();
      break;
    case "bandwidth":
      Misc_ThisMonthCalendar("Calendar of Bandwidth Usage this Month", "Calendar of Bandwidth Usage", "iBW");
      break;
    case "hits":
      Misc_ThisMonthCalendar("Calendar of Hits this Month", "Calendar of Hits", "iHits");
      break;
    case "pages":
      Misc_ThisMonthCalendar("Calendar of Page Views this Month", "Calendar of Page Views", "iPages");
      break;
    case "visits":
      Misc_ThisMonthCalendar("Calendar of Visitors this Month", "Calendar of Visitors", "iVisits");
      break;
  }
  $("#content").fadeIn(g_iFadeSpeed);
}

function PageLayout_Time(sPage) {
  var aTable = DrawTable_Time(sPage);
  var sHTML = "<h2>" + Lang("Visitors over 24 Hours") + "</h2>" +
              "<div id=\"graph\" class=\"graph\">&nbsp;</div>" +
              "<div class=\"tableFull\">" + aTable[1] + "</div>";
  $("#content").html(sHTML);
  if (aTable[0] == true) {
    $(".tablesorter").tablesorter({ headers:{1:{sorter:"commaNumber"},2:{sorter:false},3:{sorter:false},4:{sorter:"commaNumber"},5:{sorter:false},6:{sorter:false},7:{sorter:'bandwidth'},8:{sorter:false},9:{sorter:false},10:{sorter:"commaNumber"},11:{sorter:"commaNumber"},12:{sorter:'bandwidth'}},sortList: [[0,0]],textExtraction:function(node){return node.innerHTML.replace(',', '');}, widgets: ['zebra'] });
  }
  DrawGraph_Time();
  $("#content").fadeIn(g_iFadeSpeed);
}

function PagingInputNumber(oEvent, oInput, sType) {
  var iCode = (oEvent.charCode || oEvent.keyCode);
  if (iCode == 13) {
    var iValue = parseFloat($(oInput).val());
    if (isNaN(iValue) == true) { return false; }
    if (iValue < 1) { return false; }
    if (iValue != Math.round(iValue)) { return false; }
    switch (sType) {
      case "keyphrases":
        if (iValue > (Math.floor((oStatistics.oKeyphrases.aData.length - 1) / oPaging.oKeyphrases.iRowsPerPage) + 1)) { return false; }
        RedrawTable_Keyphrases("iCurrPage", (iValue - 1));
        break;
      case "keywords":
        if (iValue > (Math.floor((oStatistics.oKeywords.aData.length - 1) / oPaging.oKeywords.iRowsPerPage) + 1)) { return false; }
        RedrawTable_Keywords("iCurrPage", (iValue - 1));
        break;
    }
  }
  if ((iCode == 8) || (iCode == 9) || ((iCode > 34) && (iCode < 38)) || (iCode == 39) || (iCode == 46) || ((iCode > 47) && (iCode < 58))) {
    return true;
  } else {
    return false;
  }
}

function Paging_Keyphrases() {
  // get values
  iTotalFreq = oStatistics.oKeyphrases.iTotalFreq;
  switch (oPaging.oKeyphrases.sSort) {
    case "freqASC":
      var sKeyphraseClass = "";
      var sFrequencyClass = " headerSortDown";
      var sKeyphraseSort = "wordDESC";
      var sFrequencySort = "freqDESC";
      var aData = oStatistics.oKeyphrases.aData;
      var iDisplayOrder = -1;
      break;
    case "freqDESC":
      var sKeyphraseClass = "";
      var sFrequencyClass = " headerSortUp";
      var sKeyphraseSort = "wordDESC";
      var sFrequencySort = "freqASC";
      var aData = oStatistics.oKeyphrases.aData;
      var iDisplayOrder = 1;
      break;
    case "wordASC":
      var sKeyphraseClass = " headerSortDown";
      var sFrequencyClass = "";
      var sKeyphraseSort = "wordDESC";
      var sFrequencySort = "freqDESC";
      var aData = oStatistics.oKeyphrasesAlphabetical.aData;
      var iDisplayOrder = -1;
      break;
    case "wordDESC":
      var sKeyphraseClass = " headerSortUp";
      var sFrequencyClass = "";
      var sKeyphraseSort = "wordASC";
      var sFrequencySort = "freqDESC";
      var aData = oStatistics.oKeyphrasesAlphabetical.aData;
      var iDisplayOrder = 1;
      break;
  }

  // create header
  var sDesc = (Lang("Showing [START] to [END] of [TOTAL] keyphrases")).replace("[TOTAL]", aData.length);
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th class=\"header" + sKeyphraseClass + "\" onclick=\"RedrawTable_Keyphrases('sSort', '" + sKeyphraseSort + "')\" width=\"80%\">" + Lang("Keyphrase") + "</th>" +
              "<th class=\"header" + sFrequencyClass + "\" onclick=\"RedrawTable_Keyphrases('sSort', '" + sFrequencySort + "')\" width=\"10%\">" + Lang("Frequency") + "</th>" +
              "<th class=\"noborder\" width=\"10%\">&nbsp;</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  if (iDisplayOrder == 1) {
    var iStart = (oPaging.oKeyphrases.iCurrPage * oPaging.oKeyphrases.iRowsPerPage);
    var iEnd = (iStart + oPaging.oKeyphrases.iRowsPerPage);
    if (iEnd > aData.length) {
      iEnd = aData.length;
    }
    sDesc = sDesc.replace("[START]", iStart + 1).replace("[END]", iEnd);
    for (var i = iStart; i < iEnd; i++) {
      aHTML.push(((i % 2 == 0) ? "<tr>" : "<tr class=\"odd\">") +
                 "<td>" + aData[i].sPhrase + "</td>" +
                 "<td class=\"right\">" + NumberFormat(aData[i].iFreq, 0) + "</td>" +
  			         "<td class=\"noborder right\">" + ((aData[i].iFreq / iTotalFreq) * 100).toFixed(1) + "%</td>" +
                 "</tr>\n");
    }
  } else {
    if (aData.length > 0) {
      var iStart = (aData.length - 1) - (oPaging.oKeyphrases.iCurrPage * oPaging.oKeyphrases.iRowsPerPage);
      var iEnd = (iStart - oPaging.oKeyphrases.iRowsPerPage);
      if (iEnd < -1) {
        iEnd = -1;
      }
      sDesc = sDesc.replace("[START]", iStart + 1).replace("[END]", iEnd + 2);
      for (var i = iStart; i > iEnd; i--) {
        aHTML.push(((i % 2 == 0) ? "<tr>" : "<tr class=\"odd\">") +
                   "<td>" + aData[i].sPhrase + "</td>" +
                   "<td class=\"right\">" + NumberFormat(aData[i].iFreq, 0) + "</td>" +
    			         "<td class=\"noborder right\">" + ((aData[i].iFreq / iTotalFreq) * 100).toFixed(1) + "%</td>" +
                   "</tr>\n");
      }
    }
  }

  // output
  if (aHTML.length > 0) {
    var iMaxPage = Math.floor((aData.length - 1) / oPaging.oKeyphrases.iRowsPerPage);
    var sNavigation = "<div id=\"paging\"><span>" + sDesc + "</span>";
    if (oPaging.oKeyphrases.iCurrPage > 0) {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/first.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/first_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/first.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keyphrases('iCurrPage', 0)\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/prev.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/prev_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/prev.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keyphrases('iCurrPage', " + (oPaging.oKeyphrases.iCurrPage - 1) + ")\" />";
    } else {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/first_off.gif\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/prev_off.gif\" />";
    }
    sNavigation += "<span><input type=\"text\" value=\"" + (oPaging.oKeyphrases.iCurrPage + 1) + "\" onkeypress=\"return PagingInputNumber(event, this, 'keyphrases');\" />" + " / " + (iMaxPage + 1) + "</span>";
    if (oPaging.oKeyphrases.iCurrPage < iMaxPage) {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/next.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/next_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/next.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keyphrases('iCurrPage', " + (oPaging.oKeyphrases.iCurrPage + 1) + ")\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/last.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/last_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/last.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keyphrases('iCurrPage', " + iMaxPage + ")\" />";
    } else {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/next_off.gif\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/last_off.gif\" />";
    }
    sNavigation += "</div>";
    return (sHTML + aHTML.join("\n") + "</tbody></table>" + sNavigation);
  } else {
    return (sHTML + "<tr><td class=\"center\" colspan=\"3\">" + Lang("There is no data to display") + "</td></tr></tbody></table>");
  }
}

function Paging_Keywords() {
  // get values
  iTotalFreq = oStatistics.oKeywords.iTotalFreq;
  switch (oPaging.oKeywords.sSort) {
    case "freqASC":
      var sKeywordClass = "";
      var sFrequencyClass = " headerSortDown";
      var sKeywordSort = "wordDESC";
      var sFrequencySort = "freqDESC";
      var aData = oStatistics.oKeywords.aData;
      var iDisplayOrder = -1;
      break;
    case "freqDESC":
      var sKeywordClass = "";
      var sFrequencyClass = " headerSortUp";
      var sKeywordSort = "wordDESC";
      var sFrequencySort = "freqASC";
      var aData = oStatistics.oKeywords.aData;
      var iDisplayOrder = 1;
      break;
    case "wordASC":
      var sKeywordClass = " headerSortDown";
      var sFrequencyClass = "";
      var sKeywordSort = "wordDESC";
      var sFrequencySort = "freqDESC";
      var aData = oStatistics.oKeywordsAlphabetical.aData;
      var iDisplayOrder = -1;
      break;
    case "wordDESC":
      var sKeywordClass = " headerSortUp";
      var sFrequencyClass = "";
      var sKeywordSort = "wordASC";
      var sFrequencySort = "freqDESC";
      var aData = oStatistics.oKeywordsAlphabetical.aData;
      var iDisplayOrder = 1;
      break;
  }

  // create header
  var sDesc = (Lang("Showing [START] to [END] of [TOTAL] keywords")).replace("[TOTAL]", aData.length);
  var sHTML = "<table class=\"tablesorter\" cellspacing=\"0\">\n" +
              "<thead><tr>" +
              "<th class=\"header" + sKeywordClass + "\" onclick=\"RedrawTable_Keywords('sSort', '" + sKeywordSort + "')\" width=\"80%\">" + Lang("Keyword") + "</th>" +
              "<th class=\"header" + sFrequencyClass + "\" onclick=\"RedrawTable_Keywords('sSort', '" + sFrequencySort + "')\" width=\"10%\">" + Lang("Frequency") + "</th>" +
              "<th class=\"noborder\" width=\"10%\">&nbsp;</th>" +
              "</tr></thead>\n" +
              "<tbody>";

  // create table body
  aHTML = new Array();
  if (iDisplayOrder == 1) {
    var iStart = (oPaging.oKeywords.iCurrPage * oPaging.oKeywords.iRowsPerPage);
    var iEnd = (iStart + oPaging.oKeywords.iRowsPerPage);
    if (iEnd > aData.length) {
      iEnd = aData.length;
    }
    sDesc = sDesc.replace("[START]", iStart + 1).replace("[END]", iEnd);
    for (var i = iStart; i < iEnd; i++) {
      aHTML.push(((i % 2 == 0) ? "<tr>" : "<tr class=\"odd\">") +
                 "<td>" + aData[i].sWord + "</td>" +
                 "<td class=\"right\">" + NumberFormat(aData[i].iFreq, 0) + "</td>" +
  			         "<td class=\"noborder right\">" + ((aData[i].iFreq / iTotalFreq) * 100).toFixed(1) + "%</td>" +
                 "</tr>\n");
    }
  } else {
    if (aData.length > 0) {
      var iStart = (aData.length - 1) - (oPaging.oKeywords.iCurrPage * oPaging.oKeywords.iRowsPerPage);
      var iEnd = (iStart - oPaging.oKeywords.iRowsPerPage);
      if (iEnd < -1) {
        iEnd = -1;
      }
      sDesc = sDesc.replace("[START]", iStart + 1).replace("[END]", iEnd + 2);
      for (var i = iStart; i > iEnd; i--) {
        aHTML.push(((i % 2 == 0) ? "<tr>" : "<tr class=\"odd\">") +
                   "<td>" + aData[i].sWord + "</td>" +
                   "<td class=\"right\">" + NumberFormat(aData[i].iFreq, 0) + "</td>" +
    			         "<td class=\"noborder right\">" + ((aData[i].iFreq / iTotalFreq) * 100).toFixed(1) + "%</td>" +
                   "</tr>\n");
      }
    }
  }

  // output
  if (aHTML.length > 0) {
    var iMaxPage = Math.floor((aData.length - 1) / oPaging.oKeywords.iRowsPerPage);
    var sNavigation = "<div id=\"paging\"><span>" + sDesc + "</span>";
    if (oPaging.oKeywords.iCurrPage > 0) {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/first.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/first_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/first.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keywords('iCurrPage', 0)\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/prev.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/prev_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/prev.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keywords('iCurrPage', " + (oPaging.oKeywords.iCurrPage - 1) + ")\" />";
    } else {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/first_off.gif\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/prev_off.gif\" />";
    }
    sNavigation += "<span><input type=\"text\" value=\"" + (oPaging.oKeywords.iCurrPage + 1) + "\" onkeypress=\"return PagingInputNumber(event, this, 'keywords');\" />" + " / " + (iMaxPage + 1) + "</span>";
    if (oPaging.oKeywords.iCurrPage < iMaxPage) {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/next.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/next_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/next.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keywords('iCurrPage', " + (oPaging.oKeywords.iCurrPage + 1) + ")\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/last.gif\" onmouseover=\"this.src='themes/" + sThemeDir + "/paging/last_on.gif'\" onmouseout=\"this.src='themes/" + sThemeDir + "/paging/last.gif'\" style=\"cursor: pointer;\" onclick=\"RedrawTable_Keywords('iCurrPage', " + iMaxPage + ")\" />";
    } else {
      sNavigation += "<img src=\"themes/" + sThemeDir + "/paging/next_off.gif\" />" +
                     "<img src=\"themes/" + sThemeDir + "/paging/last_off.gif\" />";
    }
    sNavigation += "</div>";
    return (sHTML + aHTML.join("\n") + "</tbody></table>" + sNavigation);
  } else {
    return (sHTML + "<tr><td class=\"center\" colspan=\"3\">" + Lang("There is no data to display") + "</td></tr></tbody></table>");
  }
}

function PopulateData_AllMonths(sPage) {
  $("#loading").show();

	// create data objects
	var oAM = { "aData":[], "aYearDayCount":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("ALLMONTHS"),
    success: function(oXML){
      // CheckLastUpdate(oXML); disabled until update problem is fixed. only affects this call.

      var aTemp = [];
      var iCurrentYear = 0;
      $(oXML).find('month').each(function() {
        dtTemp = new Date(parseInt($(this).attr("year")), (parseInt($(this).attr("month")) - 1), 1);

        // days in month
        iDaysInMonth = parseFloat($(this).attr("daysinmonth"));

        // push items onto array
        aTemp.push({ "dtDate"       : new Date(dtTemp.getFullYear(), dtTemp.getMonth(), 1),
                     "iMonth"       : $(this).attr("month"),
                     "iYear"        : $(this).attr("year"),
                     "iDaysInMonth" : iDaysInMonth,
            	 		   "iVisits"      : $(this).attr("visits"),
            	 		   "iUniques"     : $(this).attr("uniques"),
            			   "iPages"       : $(this).attr("pages"),
            			   "iHits"        : $(this).attr("hits"),
                     "iBW"          : $(this).attr("bw"),
                     "iDaysInMonth" : iDaysInMonth
                  });

        // count days in year
        if (iCurrentYear != dtTemp.getFullYear()) {
          iCurrentYear = dtTemp.getFullYear();
          oAM.aYearDayCount[iCurrentYear] = iDaysInMonth;
        } else {
          oAM.aYearDayCount[iCurrentYear] += iDaysInMonth;
        }
      });

      // apply data
      oAM.aData = aTemp;
      oStatistics.oAllMonths = oAM;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Browser(sPage) {
  $("#loading").show();

	// create data objects
	var oB = { "iTotalHits":0, "aData":[], "aFamily":[] };

	// pre-populate browser families
  oB.aFamily = gc_aBrowserFamily;

  // do ajax call
  $.ajax({
    type: "GET",
    url: XMLURL("BROWSER"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        var sBrowser     = $(this).attr("id");
        var iHits        = parseInt($(this).attr("hits"));

        // find family browser belongs to
        var bFamilyFound = false;
        var sFamily = "";
        for (var iRow in oB.aFamily) {
          if (sBrowser.substr(0, oB.aFamily[iRow].sBrowser.length) == oB.aFamily[iRow].sBrowser) {
            // change name
            sBrowser = sBrowser.substr(oB.aFamily[iRow].sBrowser.length);
            sBrowser = (gc_aBrowserFamilyCaption[oB.aFamily[iRow].sBrowser] + " " + sBrowser);

            // add totals
            oB.aFamily[iRow].iHits += iHits;
            sFamily = oB.aFamily[iRow].sBrowser;
            bFamilyFound = true;
            break;
          }
        }
        if (bFamilyFound != true) {
          oB.aFamily[oB.aFamily.length - 1].iHits += iHits;
          sFamily = "Other Browsers";
        }

        // increment totals
        oB.iTotalHits += iHits;

        // populate array
        oB.aData.push({ "sBrowser":sBrowser,
                        "iHits":iHits,
                        "sFamily":sFamily });
      });

      // apply data
      oB.aData.sort(Sort_Hits);
      oB.aFamily.sort(Sort_Hits);
      oStatistics.oBrowser = oB;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Country(sPage) {
  $("#loading").show();

	// create data objects
	var oC = { "bPopulated":false, "iTotalPages":0, "iTotalHits":0, "iTotalBW":0, "aData":[] };
	oC.oContinent = { "Africa":{}, "Antartica":{}, "Asia":{}, "Europe":{}, "North America":{}, "Oceania":{}, "South America":{}, "Other":{} };
	for (var sContinent in oC.oContinent) {
		oC.oContinent[sContinent] = { "iTotalPages":0, "iTotalHits":0, "iTotalBW":0 };
	}

  $.ajax({
    type: "GET",
    url: XMLURL("DOMAIN"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sCountryCode = $(this).attr("id");
        var sCountryName = gc_aCountryName[sCountryCode];
        if (typeof gc_aCountryName[sCountryCode] == "undefined") {
          sCountryName = ("Unknown (code: " + sCountryCode.toUpperCase() + ")");
        }
        var sContinent   = gc_aCountryContinent[sCountryCode];
        if (typeof gc_aContinents[sContinent] == "undefined") {
          sContinent = "Other";
        }
        var iPages       = parseInt($(this).attr("pages"));
        var iHits        = parseInt($(this).attr("hits"));
        var iBW          = parseInt($(this).attr("bw"));

        // increment totals
        oC.iTotalPages += iPages;
        oC.iTotalHits += iHits;
        oC.iTotalBW += iBW;
        oC.oContinent[sContinent].iTotalPages += iPages;
        oC.oContinent[sContinent].iTotalHits += iHits;
        oC.oContinent[sContinent].iTotalBW += iBW;

        // populate array
        oC.aData.push({ "sCountryCode":sCountryCode,
                        "sCountryName":sCountryName,
                        "sContinent":sContinent,
                        "iPages":iPages,
                        "iHits":iHits,
                        "iBW":iBW } );
      });

      // apply data
      oC.aData.sort(Sort_Pages);
      oStatistics.oCountry = oC;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Filetypes(sPage) {
  $("#loading").show();

	// create data objects
	var oF = { "iTotalHits":0, "iTotalBW":0, "iTotalNonCompBW":0, "iTotalCompBW":0, "aData":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("FILETYPES"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sFiletype    = $(this).attr("id");
        var sDescription = gc_aFiletypeDesc[sFiletype];
        if (typeof gc_aFiletypeDesc[sFiletype] == "undefined") {
          sDescription = "&nbsp;";
        }
        if (sFiletype == "Unknown") {
          sFiletype = "&nbsp;";
          sDescription = "Unknown";
        }
        var iHits        = parseInt($(this).attr("hits"));
        var iBW          = parseInt($(this).attr("bw"));
        var iNonCompBW   = parseInt($(this).attr("noncompressedbw"));
        var iCompBW      = parseInt($(this).attr("compressedbw"));

        // increment totals
        oF.iTotalHits += iHits;
        oF.iTotalBW += iBW;
        oF.iTotalNonCompBW += iNonCompBW;
        oF.iTotalCompBW += iCompBW;

        // populate array
        oF.aData.push({ "sFiletype":sFiletype,
                        "sDescription":sDescription,
                        "iHits":iHits,
                        "iBW":iBW,
                        "iNonCompBW":iNonCompBW,
                        "iCompBW":iCompBW } );
      });

      // apply data
      oF.aData.sort(Sort_Hits);
      oStatistics.oFiletypes = oF;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Keyphrases(sPage) {
  $("#loading").show();

	// create data objects
	var oKP = { iMaxFreq: 0, iTotalFreq:0, aData:[] };
	var oKPAlpha = { aData:[] };

  $.ajax({
    type: "GET",
    url: XMLURL("SEARCHWORDS"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sPhrase      = $(this).attr("phrase").split("+").join(" ").split("%").join("%&#8203;");
        var iFreq        = parseInt($(this).attr("freq"));

        // increment totals
        oKP.iTotalFreq += iFreq;
        if (iFreq > oKP.iMaxFreq) {
          oKP.iMaxFreq = iFreq;
        }
        // populate array
        oKP.aData.push({ "sPhrase" : sPhrase,
                         "iFreq"   : iFreq } );
        oKPAlpha.aData.push({ sWord : sPhrase,
                              iFreq : iFreq } );
      });

      // apply data
      oKP.aData.sort(Sort_Freq);
      oKPAlpha.aData.sort(Sort_Phrase);
      oStatistics.oKeyphrases = oKP;
      oStatistics.oKeyphrasesAlphabetical = oKPAlpha;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Keywords(sPage) {
  $("#loading").show();

	// create data objects
	var oKW = { iMaxFreq: 0, iTotalFreq:0, aData:[] };
	var oKWAlpha = { aData:[] };

  $.ajax({
    type: "GET",
    url: XMLURL("KEYWORDS"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sWord        = $(this).attr("word").split("%").join("%&#8203;");
        var iFreq        = parseInt($(this).attr("freq"));

        // increment totals
        oKW.iTotalFreq += iFreq;
        if (iFreq > oKW.iMaxFreq) {
          oKW.iMaxFreq = iFreq;
        }

        // populate array
        oKW.aData.push({ sWord : sWord,
                         iFreq : iFreq } );
        oKWAlpha.aData.push({ sWord : sWord,
                              iFreq : iFreq } );
      });

      // apply data
      oKW.aData.sort(Sort_Freq);
      oKWAlpha.aData.sort(Sort_Word);
      oStatistics.oKeywords = oKW;
      oStatistics.oKeywordsAlphabetical = oKWAlpha;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_OperatingSystems(sPage) {
  $("#loading").show();

	// create data objects
	var oOS = { "iTotalHits":0, "aData":[], "aFamily":[] };

	// pre-populate browser families
  oOS.aFamily = gc_aOSFamily;

  // do ajax call
  $.ajax({
    type: "GET",
    url: XMLURL("OS"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        var sOperatingSystem = $(this).attr("id");
        var iHits            = parseInt($(this).attr("hits"));

        // find family OS belongs to
        var bFamilyFound = false;
        var sFamily = "";
        for (var iRow in oOS.aFamily) {
          if (sOperatingSystem.substr(0, oOS.aFamily[iRow].sOperatingSystem.length) == oOS.aFamily[iRow].sOperatingSystem) {
            // change name
            sOperatingSystem = sOperatingSystem.substr(oOS.aFamily[iRow].sOperatingSystem.length);
            switch (oOS.aFamily[iRow].sOperatingSystem) {
              case "mac":
                sOperatingSystem = (sOperatingSystem.substr(0, 1).toUpperCase() + sOperatingSystem.substr(1));
                switch (sOperatingSystem) {
                  case "Intosh":
                    sOperatingSystem = "Macintosh";
                    break;
                  case "Osx":
                    sOperatingSystem = "OSX";
                    break;
                }
                break;
              case "sun":
              case "win":
                sOperatingSystem = sOperatingSystem.toUpperCase();
                break;
              default:
                sOperatingSystem = (sOperatingSystem.substr(0, 1).toUpperCase() + sOperatingSystem.substr(1));
            }
            sOperatingSystem = (gc_aOSFamilyCaption[oOS.aFamily[iRow].sOperatingSystem] + " " + sOperatingSystem);

            // add totals
            oOS.aFamily[iRow].iHits += iHits;
            sFamily = oOS.aFamily[iRow].sOperatingSystem;
            bFamilyFound = true;
            break;
          }
        }
        if (bFamilyFound != true) {
          oOS.aFamily[oOS.aFamily.length - 1].iHits += iHits;
          sFamily = "Other OS";
        }

        // increment totals
        oOS.iTotalHits += iHits;

        // populate array
        oOS.aData.push({ "sOperatingSystem":sOperatingSystem,
                         "iHits":iHits,
                         "sFamily":sFamily });
      });

      // apply data
      oOS.aData.sort(Sort_Hits);
      oOS.aFamily.sort(Sort_Hits);
      oStatistics.oOperatingSystems = oOS;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_PageRefs(sPage) {
  $("#loading").show();

	// create data objects
	var oPR = { "iTotalPages":0, "iTotalHits":0, "aData":[], "aDataDomain":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("PAGEREFS"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sReferrer    = $(this).attr("url");
        var iPages       = parseInt($(this).attr("pages"));
        var iHits        = parseInt($(this).attr("hits"));

        // increment totals
        oPR.iTotalPages += iPages;
        oPR.iTotalHits += iHits;

        // populate array
        oPR.aData.push({ "sURL"         : sReferrer,
                         "sVisibleURL"  : sReferrer.split("/").join("&#8203;/").split("-").join("-&#8203;").split("_").join("_&#8203;"),
                         "iPages"       : iPages,
                         "iHits"        : iHits } );

        // populate domain array
        var aTemp = sReferrer.split("/");
        var sDomain = (aTemp[0] + "//" + aTemp[2]);
        var sVisibleDomain = aTemp[2].replace(/^www./, "");
        $bExists = false;
        for (var iRow in oPR.aDataDomain) {
          if (oPR.aDataDomain[iRow].sVisibleURL == sVisibleDomain) {
            oPR.aDataDomain[iRow].iPages += iPages;
            oPR.aDataDomain[iRow].iHits += iHits;
            $bExists = true;
            break
          }
        }
        if ($bExists != true) {
          oPR.aDataDomain.push({ "sURL"          : sDomain,
                                 "sVisibleURL"   : aTemp[2].replace(/^www./, ""),
                                 "iPages"        : iPages,
                                 "iHits"         : iHits });
        }
      });

      // apply data
			oPR.aData.sort(Sort_Pages);
      oStatistics.oPageRefs = oPR;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_PageRefsSE() {
  $("#loading").show();

	// create data objects
	var oPR = { "iTotalPages":0, "iTotalHits":0, "aData":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("SEREFERRALS"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sReferrer    = $(this).attr("id");
        var sURL         = sReferrer;
        var sImage       = "&nbsp;";
        var iPages       = parseInt($(this).attr("pages"));
        var iHits        = parseInt($(this).attr("hits"));

        // find if exists in array
        for (var i = 0; i < gc_aSearchEngines.length; i++) {
          if (gc_aSearchEngines[i].sCode == sReferrer) {
            sReferrer = gc_aSearchEngines[i].sName;
            sURL      = gc_aSearchEngines[i].sURL;
            sImage = "<img src=\"themes/" + sThemeDir + "/searchengines/" + gc_aSearchEngines[i].sImage + ".gif\" alt=\"" + sReferrer + "\" />";
            break;
          }
        }

        // increment totals
        oPR.iTotalPages += iPages;
        oPR.iTotalHits += iHits;

        // populate array
        oPR.aData.push({ "sReferrer"    : sReferrer,
                         "sURL"         : sURL,
                         "sImage"       : sImage,
                         "iPages"       : iPages,
                         "iHits"        : iHits } );
      });

      // apply data
			oPR.aData.sort(Sort_Pages);
      oStatistics.oPageRefsSE = oPR;
      $("#loading").hide();
      DrawPage("pagerefs.se");
    }
  });
}

function PopulateData_Pages(sPage) {
  $("#loading").show();

	// create data objects
	var oP = { iTotalPages:0, iTotalBW:0, iTotalEntry:0, iTotalExit:0, aDataPages:[], aDataBW:[], aDataEntry:[], aDataExit:[] };

  // do ajax call
  $.ajax({
    type: "GET",
    url: XMLURL("PAGES"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      // retrieve totals
      var oXMLTotals = $(oXML).find("totals");
      oP.iTotalPages = parseInt(oXMLTotals.attr("pages"));
      oP.iTotalBW = parseInt(oXMLTotals.attr("bw"));
      oP.iTotalEntry = parseInt(oXMLTotals.attr("entry"));
      oP.iTotalExit = parseInt(oXMLTotals.attr("exit"));

      // extract data blocks
      oP.aDataPages = ExtractData($(oXML).find('data_pages'));
      oP.aDataBW = ExtractData($(oXML).find('data_bw'));
      oP.aDataEntry = ExtractData($(oXML).find('data_entry'));
      oP.aDataExit = ExtractData($(oXML).find('data_exit'));

      // apply data
      oStatistics.oPages = oP;
      $("#loading").hide();
      DrawPage(sPage);

      function ExtractData(oXMLBlock) {
        var aData = [];

        $(oXMLBlock).find('item').each(function() {
          aData.push({ "sURL"   : $(this).attr("url"),
                       "iPages" : parseInt($(this).attr("pages")),
                       "iBW"    : parseInt($(this).attr("bw")),
                       "iEntry" : parseInt($(this).attr("entry")),
                       "iExit"  : parseInt($(this).attr("exit")) });
        });

        return aData;
      }
    }
  });
}

function PopulateData_Robots(sPage) {
  $("#loading").show();

	// create data objects
	var oR = { "iTotalHits":0, "iTotalBW":0, "dtLastVisit":0, "iTotalRobotsTXT":0, "aData":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("ROBOT"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sRobot       = $(this).attr("id");
        var iHits        = parseInt($(this).attr("hits"));
        var iBW          = parseInt($(this).attr("bw"));
        var dtLastVisit  = parseInt($(this).attr("lastvisit"));
        var iRobotsTXT   = parseInt($(this).attr("robotstxt"));

        // increment totals
        oR.iTotalHits += iHits;
        oR.iTotalBW += iBW;
        oR.iTotalRobotsTXT += iRobotsTXT;

        // populate array
        oR.aData.push({ "sRobot":sRobot,
                        "iHits":iHits,
                        "iBW":iBW,
                        "dtLastVisit":dtLastVisit,
                        "iRobotsTXT":iRobotsTXT } );
      });

      // apply data
      oStatistics.oRobots = oR;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Session() {
  $("#loading").show();

	// create data objects
	var oS = { "iTotalFreq":0, "aData":{s1h:0, s5mn15mn:0, s15mn30mn:0, s30s2mn:0, s0s30s:0, s2mn5mn:0, s30mn1h:0 } };

  $.ajax({
    type: "GET",
    url: XMLURL("SESSION"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sRange = ("s" + $(this).attr("range").replace("+", "").replace("-", ""));
        oS.aData[sRange] = parseInt($(this).attr("freq"));

        // increment totals
        oS.iTotalFreq += oS.aData[sRange];
      });

      // apply data
      oStatistics.oSession = oS;
      $("#loading").hide();
      DrawPage("session");
    }
  });
}

function PopulateData_Status(sPage) {
  $("#loading").show();

	// create data objects
	var oS = { "iTotalHits":0, "iTotalBW":0, "aData":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("ERRORS"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sCode        = $(this).attr("id");
        var sDescription = gc_aHTTPStatus[sCode];
        if (typeof gc_aHTTPStatus[sCode] == "undefined") {
          sDescription = "&nbsp;";
        }
        var iHits        = parseInt($(this).attr("hits"));
        var iBW          = parseInt($(this).attr("bw"));

        // increment totals
        oS.iTotalHits += iHits;
        oS.iTotalBW += iBW;

        // populate array
        oS.aData.push({ "sCode":sCode,
                        "sDescription":sDescription,
                        "iHits":iHits,
                        "iBW":iBW } );
      });

      // apply data
      oS.aData.sort(Sort_Hits);
      oStatistics.oStatus = oS;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Status404(sPage) {
  $("#loading").show();

	// create data objects
	var oS = { "iTotalHits":0, "aData":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("SIDER_404"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      $(oXML).find('item').each(function() {
        // collect values
        var sURL         = $(this).attr("url");
        var iHits        = parseInt($(this).attr("hits"));
        var sReferrer    = $(this).attr("referrer");

        // increment totals
        oS.iTotalHits += iHits;

        // populate array
        oS.aData.push({ "sURL":sURL.split("/").join("&#8203;/").split("-").join("-&#8203;").split("_").join("_&#8203;"),
                        "iHits":iHits,
                        "sReferrer":sReferrer,
                        "sReferrerVisible":sReferrer.split("/").join("&#8203;/").split("-").join("-&#8203;").split("_").join("_&#8203;") } );
      });

      // apply data
      oS.aData.sort(Sort_Hits);
      oStatistics.oStatus404 = oS;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_ThisMonth(sPage) {
  $("#loading").show();

	// create data objects
	var oTM = { "iTotalPages":0, "iTotalHits":0, "iTotalBW":0, "iTotalVisits":0, "aData":[] };

  $.ajax({
    type: "GET",
    url: XMLURL("DAY"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      var aTemp1 = [];
      var iMaxDate = 0;
      $(oXML).find('item').each(function() {
        // collect values
        var sDate        = $(this).attr("date");
        var iVisits      = parseInt($(this).attr("visits"));
    		var iPages       = parseInt($(this).attr("pages"));
    		var iHits        = parseInt($(this).attr("hits"));
        var iBW          = parseInt($(this).attr("bw"));

        // increment totals
        oTM.iTotalVisits += iVisits;
    		oTM.iTotalPages += iPages;
    		oTM.iTotalHits += iHits;
        oTM.iTotalBW += iBW;

        // create javascript date
        dtDate = new Date(sDate.substr(0,4),
                          (parseInt(StripLeadingZeroes(sDate.substr(4,2))) - 1),
                          sDate.substr(6,2));

        // populate array
        aTemp1.push({ "dtDate"   : dtDate,
                      "iVisits"  : iVisits,
            				  "iPages"   : iPages,
            				  "iHits"    : iHits,
                      "iBW"      : iBW } );
        dtMaxDate = dtDate;
      });

      // populate complete array (including empty values)
      var aTemp2 = [];
      var iPointer = 0;
      for (var iIndex = 0; iIndex < dtMaxDate.getDate(); iIndex++) {
        dtExpectedDate = new Date(dtMaxDate.getFullYear(), dtMaxDate.getMonth(), (iIndex + 1));
        if (aTemp1[iPointer].dtDate.valueOf() == dtExpectedDate.valueOf()) {
          aTemp2.push({ "dtDate"  : new Date(dtMaxDate.getFullYear(), dtMaxDate.getMonth(), (iIndex + 1)),
                        "iVisits" : aTemp1[iPointer].iVisits,
              				  "iPages"  : aTemp1[iPointer].iPages,
              				  "iHits"   : aTemp1[iPointer].iHits,
                        "iBW"     : aTemp1[iPointer].iBW } );
          iPointer++;
        } else {
          aTemp2.push({ "dtDate"  : new Date(dtMaxDate.getFullYear(), dtMaxDate.getMonth(), (iIndex + 1)),
                        "iVisits" : 0,
              				  "iPages"  : 0,
              				  "iHits"   : 0,
                        "iBW"     : 0 } );
        }
      }

      // apply data
      oTM.aData = aTemp2;
      oStatistics.oThisMonth = oTM;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function PopulateData_Time(sPage) {
  $("#loading").show();

	// create data objects
	var oT = { "iTotalPages":0, "iTotalHits":0, "iTotalBW":0, "iTotalNVPages":0, "iTotalNVHits":0, "iTotalNVBW":0, "aData":[]  };

  $.ajax({
    type: "GET",
    url: XMLURL("TIME"),
    success: function(oXML){
      CheckLastUpdate(oXML);

      var aTemp = [];
      $(oXML).find('item').each(function() {
        // collect values
        var iHour        = parseInt($(this).attr("hour"));
        var iPages       = parseInt($(this).attr("pages"));
        var iHits        = parseInt($(this).attr("hits"));
        var iBW          = parseInt($(this).attr("bw"));
        var iNVPages     = parseInt($(this).attr("notviewedpages"));
        var iNVHits      = parseInt($(this).attr("notviewedhits"));
        var iNVBW        = parseInt($(this).attr("notviewedbw"));

        // increment totals
        oT.iTotalPages += iPages;
        oT.iTotalHits += iHits;
        oT.iTotalBW += iBW;
        oT.iTotalNVPages += iPages;
        oT.iTotalNVHits += iHits;
        oT.iTotalNVBW += iBW;

        // populate array
        oT.aData.push({ "iHour":iHour,
                        "iPages":iPages,
                        "iHits":iHits,
                        "iBW":iBW,
                        "iNVPages":iNVPages,
                        "iNVHits":iNVHits,
                        "iNVBW":iNVBW } );
      });

      // apply data
      oStatistics.oTime = oT;
      $("#loading").hide();
      DrawPage(sPage);
    }
  });
}

function RedrawTable_Keyphrases(sParam, sValue) {
  oPaging.oKeyphrases[sParam] = sValue;
  $(".tablePie").html(Paging_Keyphrases());
}

function RedrawTable_Keywords(sParam, sValue) {
  oPaging.oKeywords[sParam] = sValue;
  $(".tablePie").html(Paging_Keywords());
}

function SafeDivide(iFirst, iSecond) {
  if (iSecond != 0) {
    return (iFirst / iSecond);
  } else {
    return 0;
  }
}

function ShowTools(sID) {
  if (arguments.length > 0) {
    sToolID = sID;
  }

  // loop through items
  if ($("#tools .tool:visible").size() > 0) {
    $("#tools .tool:visible").each(function() {
      if ($(this).attr("id") == sToolID) {
        $(this).stop().slideUp(350);
      } else {
        $(this).stop().slideUp(350, ShowTools);
      }
    });
  } else {
    $("#" + sToolID).stop().slideDown(350);
  }
}

function Sort_Freq(a, b) {
  return b.iFreq - a.iFreq;
}

function Sort_Hits(a, b) {
  return b.iHits - a.iHits;
}

function Sort_Pages(a, b) {
  return b.iPages - a.iPages;
}

function Sort_Phrase(a, b) {
  return ((a.sPhrase < b.sPhrase) ? -1 : ((a.sPhrase > b.sPhrase) ? 1 : 0));
}

function Sort_Word(a, b) {
  return ((a.sWord < b.sWord) ? -1 : ((a.sWord > b.sWord) ? 1 : 0));
}

function TagCloud(sType, oData, iMaxCount) {
  // create array of top tags, sorted alpahabetically
  var aTag = [];
  var iCount = oData.aData.length;
  if (iCount > iMaxCount) { iCount = iMaxCount; }
  for (var i = 0; i < iCount; i++) {
    aTag.push(oData.aData[i]);
  }
  if (sType == "sWord") {
    aTag.sort(Sort_Word);
  } else {
    aTag.sort(Sort_Phrase);
  }

  // apply sizes
  aHTML = [];
  var iMaxSize = 60;
  var iMinSize = 11;
  var iDiff = (iMaxSize - iMinSize);
  for (var i = 0; i < iCount; i++) {
    var iSize = (Math.round((aTag[i].iFreq / oData.iMaxFreq) * iDiff) + iMinSize);
    aHTML.push("<span style=\"font-size: " + iSize + "px; line-height: " + Math.round(iSize * 1.35) + "px;\">" + aTag[i][sType] + "</span>");
  }
  return aHTML.join("\n");
}

function UpdateSite() {
  $("#loading").show();
  $.ajax({
    type: "POST",
    url: sUpdateFilename,
    data: ("config=" + g_sConfig + "&pass=" + MD5($("#password").val())),
    success: function(oXML) {
      switch ($(oXML).find('result:eq(0)').attr("type")) {
        case "bad_password":
          $("#loading").hide();
          alert( Lang("The password you entered was incorrect.") );
          break;
        case "updated":
          var sURL = "?config=" + g_sConfig + "&year=" + g_iYear + "&month=" + g_iMonth + "&view=" + g_sCurrentView + "&lang=" + g_sLanguage;
          self.location.href = sURL;
          break;
        default:
          $("#loading").hide();
      }
    }
  });
}

function UpdateSiteKeyUp(event) {
  if (event.keyCode == 13) {
    UpdateSite();
  }
}

function XMLURL(sPage) {
  var sURL = "";
  if (g_bUseStaticXML == true) {
    switch (sPage) {
      case "ALLMONTHS":
        sURL = ("static/jawstats." + g_sConfig + ".allmonths.xml?cache=" + g_dtLastUpdate);
        break;
      default:
        if (g_iMonth < 10) {
          sURL = ("static/jawstats" + g_iYear + "0" + g_iMonth + "." + g_sConfig + "." + sPage.toLowerCase() + ".xml?cache=" + g_dtLastUpdate);
        } else {
          sURL = ("static/jawstats" + g_iYear + g_iMonth + "." + g_sConfig + "." + sPage.toLowerCase() + ".xml?cache=" + g_dtLastUpdate);
        }
    }
  } else {
    switch (sPage) {
      case "ALLMONTHS":
        sURL = ("xml_history.php?config=" + g_sConfig);
        break;
      case "PAGES":
        sURL = ("xml_pages.php?config=" + g_sConfig + "&year=" + g_iYear + "&month=" + g_iMonth);
        break;
      default:
        sURL = ("xml_stats.php?config=" + g_sConfig + "&section=" + sPage + "&year=" + g_iYear + "&month=" + g_iMonth);
    }
  }
  return sURL;
}


// Other functions: get week number thanks to http://www.quirksmode.org/js/week.html
function getWeekNr(dtTempDate) {
	Year = takeYear(dtTempDate);
	Month = dtTempDate.getMonth();
	Day = dtTempDate.getDate();
	now = Date.UTC(Year,Month,Day+1,0,0,0);
	var Firstday = new Date();
	Firstday.setYear(Year);
	Firstday.setMonth(0);
	Firstday.setDate(1);
	then = Date.UTC(Year,0,1,0,0,0);
	var Compensation = Firstday.getDay();
	if (Compensation > 3) Compensation -= 4;
	else Compensation += 3;
	NumberOfWeek =  Math.round((((now-then)/86400000)+Compensation)/7);

	// my alteration to make monday-sunday calendar
	if (dtTempDate.getDay() == 0) {
  	NumberOfWeek--;
  }
	// end

	return NumberOfWeek;
}
function takeYear(dtTempDate) {
	x = dtTempDate.getYear();
	var y = x % 100;
	y += (y < 38) ? 2000 : 1900;
	return y;
}

// md5 thanks to http://www.webtoolkit.info
var MD5 = function (string) {

    function RotateLeft(lValue, iShiftBits) {
        return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
    }

    function AddUnsigned(lX,lY) {
        var lX4,lY4,lX8,lY8,lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
        if (lX4 & lY4) {
            return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        }
        if (lX4 | lY4) {
            if (lResult & 0x40000000) {
                return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            } else {
                return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
        } else {
            return (lResult ^ lX8 ^ lY8);
        }
    }

    function F(x,y,z) { return (x & y) | ((~x) & z); }
    function G(x,y,z) { return (x & z) | (y & (~z)); }
    function H(x,y,z) { return (x ^ y ^ z); }
    function I(x,y,z) { return (y ^ (x | (~z))); }

    function FF(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function GG(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function HH(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function II(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function ConvertToWordArray(string) {
        var lWordCount;
        var lMessageLength = string.length;
        var lNumberOfWords_temp1=lMessageLength + 8;
        var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
        var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
        var lWordArray=Array(lNumberOfWords-1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while ( lByteCount < lMessageLength ) {
            lWordCount = (lByteCount-(lByteCount % 4))/4;
            lBytePosition = (lByteCount % 4)*8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount)<<lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount-(lByteCount % 4))/4;
        lBytePosition = (lByteCount % 4)*8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
        lWordArray[lNumberOfWords-2] = lMessageLength<<3;
        lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
        return lWordArray;
    };

    function WordToHex(lValue) {
        var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
        for (lCount = 0;lCount<=3;lCount++) {
            lByte = (lValue>>>(lCount*8)) & 255;
            WordToHexValue_temp = "0" + lByte.toString(16);
            WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
        }
        return WordToHexValue;
    };

    function Utf8Encode(string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    };

    var x=Array();
    var k,AA,BB,CC,DD,a,b,c,d;
    var S11=7, S12=12, S13=17, S14=22;
    var S21=5, S22=9 , S23=14, S24=20;
    var S31=4, S32=11, S33=16, S34=23;
    var S41=6, S42=10, S43=15, S44=21;

    string = Utf8Encode(string);

    x = ConvertToWordArray(string);

    a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;

    for (k=0;k<x.length;k+=16) {
        AA=a; BB=b; CC=c; DD=d;
        a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
        d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
        c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
        b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
        a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
        d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
        c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
        b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
        a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
        d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
        c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
        b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
        a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
        d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
        c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
        b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
        a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
        d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
        c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
        b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
        a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
        d=GG(d,a,b,c,x[k+10],S22,0x2441453);
        c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
        b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
        a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
        d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
        c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
        b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
        a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
        d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
        c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
        b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
        a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
        d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
        c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
        b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
        a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
        d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
        c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
        b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
        a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
        d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
        c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
        b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
        a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
        d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
        c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
        b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
        a=II(a,b,c,d,x[k+0], S41,0xF4292244);
        d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
        c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
        b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
        a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
        d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
        c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
        b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
        a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
        d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
        c=II(c,d,a,b,x[k+6], S43,0xA3014314);
        b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
        a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
        d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
        c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
        b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
        a=AddUnsigned(a,AA);
        b=AddUnsigned(b,BB);
        c=AddUnsigned(c,CC);
        d=AddUnsigned(d,DD);
    }

    var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);

    return temp.toLowerCase();
}

// random stuff...
function DateSuffix(iDate) {
  switch (iDate) {
    case 1:
    case 21:
    case 31:
      return "st";
    case 2:
    case 22:
      return "nd";
    case 3:
    case 23:
      return "rd";
    default:
      return "th";
  }
}

function NumberFormat(vValue, iDecimalPlaces) {
  if (typeof iDecimalPlaces != "undefined") {
    vValue = vValue.toFixed(iDecimalPlaces);
  }
  var oRegEx = /(\d{3})(?=\d)/g;
  var aDigits = vValue.toString().split(".");
  if (aDigits[0] >= 1000) {
    aDigits[0] = aDigits[0].split("").reverse().join("").replace(oRegEx, "$1,").split("").reverse().join("");
  }
  return aDigits.join(".");
}

function StripLeadingZeroes(sString) {
  while (sString.substr(0,1) == "0") {
    sString = sString.substr(1);
  }
  return sString;
}

$.tablesorter.addParser({
  id: "commaNumber",
  is: function(s) {
    return false;
  },
  format: function(s) {
    s = s.replace(/\,/g, "");
    return s;
  },
  type: "numeric"
});