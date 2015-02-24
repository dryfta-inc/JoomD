<?php
/**
 * @Copyright
 *
 * @package     Newsscroller Self DHTML for Joomla 2.5
 * @author      Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
 * @version     Version: 2.5-1 - 02-Feb-2012
 * @link        Project Site {@link http://joomla-extensions.kubik-rubik.de/ns-newsscroller-self-dhtml}
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class NewsscrollerDhtmlHelper
{
    function scrollContent($params)
    {
        $linenews1 = $params->get('linenews1', 0);
        $linenews1status = $params->get('linenews1status', 1);
        $news1 = $params->get('news1', 0);
        $urlnews1status = $params->get('urlnews1status', 1);
        $urlnews1 = $params->get('urlnews1', 1);
        $urlnews1newwindow = $params->get('urlnews1newwindow', 1);
        $urlnews1name = $params->get('urlnews1name', 1);
        $linenews2 = $params->get('linenews2', 0);
        $linenews2status = $params->get('linenews2status', 1);
        $news2 = $params->get('news2', 0);
        $urlnews2status = $params->get('urlnews2status', 1);
        $urlnews2 = $params->get('urlnews2', 1);
        $urlnews2newwindow = $params->get('urlnews2newwindow', 1);
        $urlnews2name = $params->get('urlnews2name', 1);
        $linenews3 = $params->get('linenews3', 0);
        $linenews3status = $params->get('linenews3status', 1);
        $news3 = $params->get('news3', 0);
        $urlnews3status = $params->get('urlnews3status', 1);
        $urlnews3 = $params->get('urlnews3', 1);
        $urlnews3newwindow = $params->get('urlnews3newwindow', 1);
        $urlnews3name = $params->get('urlnews3name', 1);
        $linenews4 = $params->get('linenews4', 0);
        $linenews4status = $params->get('linenews4status', 1);
        $news4 = $params->get('news4', 0);
        $urlnews4status = $params->get('urlnews4status', 1);
        $urlnews4 = $params->get('urlnews4', 1);
        $urlnews4newwindow = $params->get('urlnews4newwindow', 1);
        $urlnews4name = $params->get('urlnews4name', 1);
        $linenews5 = $params->get('linenews5', 0);
        $linenews5status = $params->get('linenews5status', 1);
        $news5 = $params->get('news5', 0);
        $urlnews5status = $params->get('urlnews5status', 1);
        $urlnews5 = $params->get('urlnews5', 1);
        $urlnews5newwindow = $params->get('urlnews5newwindow', 1);
        $urlnews5name = $params->get('urlnews5name', 1);
        $nonews = $params->get('nonews', 1);
        $hor = $params->get('hor', 1);
        $sort = $params->get('sort', 0);
        $consecutive = $params->get('consecutive');
        $manualsorting = $params->get('manualsorting');

        $content = array();

        if($news1 != '0')
        {
            $content[0] = array('number' => 1, 'news' => $news1, 'linenewsstatus' => $linenews1status, 'linenews' => $linenews1, 'urlnewsstatus' => $urlnews1status, 'urlnews' => $urlnews1, 'urlnewsnewwindow' => $urlnews1newwindow, 'urlnewsname' => $urlnews1name);
        }

        if($news2 != '0')
        {
            $content[1] = array('number' => 2, 'news' => $news2, 'linenewsstatus' => $linenews2status, 'linenews' => $linenews2, 'urlnewsstatus' => $urlnews2status, 'urlnews' => $urlnews2, 'urlnewsnewwindow' => $urlnews2newwindow, 'urlnewsname' => $urlnews2name);
        }

        if($news3 != '0')
        {
            $content[2] = array('number' => 3, 'news' => $news3, 'linenewsstatus' => $linenews3status, 'linenews' => $linenews3, 'urlnewsstatus' => $urlnews3status, 'urlnews' => $urlnews3, 'urlnewsnewwindow' => $urlnews3newwindow, 'urlnewsname' => $urlnews3name);
        }

        if($news4 != '0')
        {
            $content[3] = array('number' => 4, 'news' => $news4, 'linenewsstatus' => $linenews4status, 'linenews' => $linenews4, 'urlnewsstatus' => $urlnews4status, 'urlnews' => $urlnews4, 'urlnewsnewwindow' => $urlnews4newwindow, 'urlnewsname' => $urlnews4name);
        }

        if($news5 != '0')
        {
            $content[4] = array('number' => 5, 'news' => $news5, 'linenewsstatus' => $linenews5status, 'linenews' => $linenews5, 'urlnewsstatus' => $urlnews5status, 'urlnews' => $urlnews5, 'urlnewsnewwindow' => $urlnews5newwindow, 'urlnewsname' => $urlnews5name);
        }

        if($sort == 0)
        {
            sort($content);
        }
        elseif($sort == 1)
        {
            rsort($content);
        }
        elseif($sort == 2)
        {
            shuffle($content);
        }
        elseif($sort == 3)
        {
            if($manualsorting != '')
            {
                $manualsortingarray = explode(",", $manualsorting);
                $manualsortingcontent = array();

                foreach($manualsortingarray as $match)
                {
                    $match = $match - 1;

                    if(isset($content[$match]) AND $content[$match] != '')
                    {
                        $manualsortingcontent[] = $content[$match];
                    }
                }

                $content = $manualsortingcontent;
            }
        }

        $html_content = '';

        if(empty($content))
        {
            $html_content .= $nonews;
        }
        else
        {
            foreach($content as $match)
            {
                if($match['linenewsstatus'] == "yes")
                {
                    $html_content .= '<h3>'.$match['linenews'].'</h3>';
                }
                $html_content .= '<p>'.nl2br($match['news']).'</p>';

                if($match['urlnewsstatus'] == "yes")
                {
                    $html_content .= '<p><a rel="nofollow" target="'.$match['urlnewsnewwindow'].'"  title="'.$match['urlnewsname'].'" href="'.$match['urlnews'].'">'.$match['urlnewsname'].'</a></p>';
                }

                if($hor == 1)
                {
                    $html_content .= "<hr />";
                }
                elseif($hor == 2)
                {
                    $html_content .= "<br /><br />";
                }
            }

            if($consecutive == 1)
            {
                for($a = 0; $a < 3; $a++)
                {
                    $html_content .= $html_content;
                }
            }
        }

        return $html_content;
    }

    function loadHeadData($params)
    {
        $bgcolor = $params->get('bgcolor', 1);
        $color = $params->get('color');
        $colortext = $params->get('colortext');
        $colorlink = $params->get('colorlink');
        $height = $params->get('height', 1);
        $textalign = $params->get('textalign', 1);
        $width = $params->get('width', 1);
        $textsize = $params->get('textsize', 1);
        $textweight = $params->get('textweight', 1);
        $fontstyle = $params->get('fontstyle', 1);

        $document = JFactory::getDocument();

        $css = '#marqueecontainer {position: relative;width:'.$width.'%;height:'.$height.'px;overflow: hidden;padding: 2px;padding-left: 4px;background-color:'.$bgcolor.';}'."\n";
        $css .= '#vmarquee {position: absolute; width: 95%; font-size:'.$textsize.'px;}'."\n";
        $css .= '#vmarquee h3 {text-align: center; color:'.$color.'; font-size:110%; font-style:'.$fontstyle.'; font-weight:700;padding-top:6px;}'."\n";
        $css .= '#vmarquee p {color:'.$colortext.'; font-weight:'.$textweight.';font-style:'.$fontstyle.';text-align:'.$textalign.';}'."\n";
        $css .= '#vmarquee p a {color:'.$colorlink.';}'."\n";
        $css .= '#vmarqueesmall {text-align: center;color:#666666;font-size:85%;}';

        $document->addStyleDeclaration($css);
    }

    function javascript($params)
    {
        $scrolldelay = $params->get('scrolldelay', 2);
        $marqueespeed = $params->get('marqueespeed');
        $pauseit = $params->get('pauseit');
        $intervaltime = $params->get('intervaltime', 60);
        ?><script type="text/javascript">// <![CDATA[
                            /**
                             * Cross browser Marquee II- Dynamic Drive (www.dynamicdrive.com)
                             */
                            var delayb4scroll="<?php echo $scrolldelay."000" ?>"
                            var marqueespeed="<?php echo $marqueespeed ?>"
                            var pauseit="<?php echo $pauseit ?>"
                            var copyspeed=marqueespeed
                            var pausespeed=(pauseit==0)?copyspeed:0
                            var actualheight=''
                            function scrollmarquee(){if(parseInt(cross_marquee.style.top)>(actualheight*(-1)+8))
                                    cross_marquee.style.top=parseInt(cross_marquee.style.top)-copyspeed+"px"
                                else
                                    cross_marquee.style.top=parseInt(marqueeheight)+8+"px"}
                                function initializemarquee(){cross_marquee=document.getElementById("vmarquee")
                                    cross_marquee.style.top=0
                                    marqueeheight=document.getElementById("marqueecontainer").offsetHeight
                                    actualheight=cross_marquee.offsetHeight
                                    if(window.opera||navigator.userAgent.indexOf("Netscape/7")!=-1){cross_marquee.style.height=marqueeheight+"px"
                                        cross_marquee.style.overflow="scroll"
                                        return}
                                    setTimeout('lefttime=setInterval("scrollmarquee()",<?php echo $intervaltime ?>)',delayb4scroll)}
                                if(window.addEventListener)
                                    window.addEventListener("load",initializemarquee,false)
                                else if(window.attachEvent)
                                    window.attachEvent("onload",initializemarquee)
                                else if(document.getElementById)
                                    window.onload=initializemarquee
                                // ]]></script>
    <?php
    }

}
