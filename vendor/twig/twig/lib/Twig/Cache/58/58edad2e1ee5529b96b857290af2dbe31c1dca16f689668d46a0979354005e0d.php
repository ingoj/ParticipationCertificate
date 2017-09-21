<?php

/* Teilnahmebescheinigung.html */
class __TwigTemplate_b794f9abdb73e31adfcf4a1398cc2c9425d5008c813ee74ba41c4379bfa904f9 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "
<body>
<div class=\"center\">

<div> <img src=\"./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/src/dhbw2.jpg\" alt=\"\"> </div>

<div class=\"head\"> Teilnahmebescheinigungs </div>

<div> <p class=\"p-first\"> <i>";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "getFullname", array()), "html", null, true);
        echo "</i>, hat am Studienvorbereitungsprogramm mit
  Schwerpunkt \"Mathematik\" auf der Lernplattform studienvorbereitung.dhbw.de
  teilgenommen. </p> </div>

<div> <p class=\"p-first\"> Die Teilnahme von <i>";
        // line 13
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "getFullname", array()), "html", null, true);
        echo "</i> vor Studienbeginn an der DHBW
  Karlsruhe zum ";
        // line 14
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["semester"]) ? $context["semester"] : null), "get", array()), "html", null, true);
        echo " umfasste:</p> </div>

 <div class=\"table1\"> <p class=\"p-first\"> <b>Studienvorbereitung - Mathematik:</b></p>
<table id=\"table1\">
  <tr>
    <td class=\"align-left\" id=\"td1\"> Abschluss
      des diagnostischen <br> Einstiegstest </td>
    <td class=\"align-center\" id=\"td2\">
      <img class=\"checkimg\" src=\"./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/src/check.png\" alt=\"\"></td>
  </tr>
  <tr>
    <td class=\"align-left\" class=\"align-top\">
      Bearbeitung von empfohlenen <br> Mathematik - Lernmodulen </td>
    <td class=\"align-center\" id=\"td3\">
      ";
        // line 28
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["result"]) ? $context["result"] : null), "learnmodule", array()), "html", null, true);
        echo " </td>
  </tr>
</table>
</div>

<div class=\"table1\"> <p class=\"p-first\"> <b>Studienvorbereitung - eMentoring: </b></p>
<table id=\"table2\">
 <tr>
   <td class=\"align-left\" id=\"td4\">
     Aktive Teilnahme an <br> Videokonferenzen </td>
   <td class=\"align-center\" id=\"td5\">
     <img class=\"checkimg\" src=\"./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/src/check.png\" alt=\"\">
     </td>
 </tr>
 <tr>
   <td class=\"align-left\" id=\"td6\">Bearbeitung von Aufgaben zur <br>
     Vertiefung von Inhalten des <br> Studienvorbereitungsprogramms</td>
   <td class=\"align-center\" id=\"td7\">
     ";
        // line 46
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["result"]) ? $context["result"] : null), "recess", array()), "html", null, true);
        echo "</td>
 </tr>
</table>
</div>

 <div class=\"date\"> <p> Karlsruhe, den ";
        // line 51
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["date"]) ? $context["date"] : null), "get", array()), "html", null, true);
        echo "</p>  </div>
<div id=\"signature\"><p> <b> ";
        // line 52
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["teacher"]) ? $context["teacher"] : null), "getFullname", array()), "html", null, true);
        echo " </b> </p>
<p> <b> ";
        // line 53
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["teacher"]) ? $context["teacher"] : null), "getFunction", array()), "html", null, true);
        echo " </b> </p></div>

</div>
<!-- ENDE SEITE EINS -->
<div class=\"pagebreak\">
</div>
<!-- BEGIN SEITE ZWEI -->
<div class=\"center\">
<div> <img src=\"./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/src/dhbw2.jpg\" alt=\"\"> </div>
<div> <h1> Erläuterungen zur Bescheinigung </h1> </div>
</div>

<div class=\"justify\"> <p> Das Studienvorbereitungsprogramm mit Schwerpunkt
  Mathematik auf der Lernplatform studienstart.dhbw.de, richtet sich an
  Studienanfänger/-innen der Wirtschaftsinformatik der
DHBW Karlsruhe. Die Teilnehmer/-innen des Programms erhalten die Möglichkeit
sich bereits vor Studienbeginn, Studientechniken anzueignen sowie das fehlende
Vorwissen im Fach \"Mathematik\" aufzuarbeiten.
Dadurch haben Studierende mehr Zeit ihre Wissenslücken in Mathematik zu
schliessen und sich mit dem neuen Lernen auseinanderzusetzen. <br> <br>

Ziel des Programms ist es, Studienanfänger/-innen vor Studienbeginn auf das Fach
 Mathematik im Studium vorzubereiten. Neben der Vermittlung von mathematikschen
 Inhalten, fördert der Online-Vorkurs
überfachliche Kompetenzen wie Zeitmanagement und Lerntechniken sowie die
Fähigkeit zum Selbststudium <br> </p>
</div>

<p>
<b>";
        // line 82
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "getFullname", array()), "html", null, true);
        echo " hat im Rahmen des Studienvorbereitungsprogramms mit
  <br>Schwerpunkt Mathematik mit folgenden Aufgabestellungen teilgenommen:</b>
</p>

<div>
  <p> <b> Studienvorbereitung - Mathematik </b></p>
<table id=\"table3\">
  <tr>
    <td id=\"td8\"> Abschluss des Diagnostischer
      Einstiegstest Mathematik</td>
    <td id=\"td9\"> ";
        // line 92
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["questions"]) ? $context["questions"] : null), "getCount", array()), "html", null, true);
        echo " Fragen aus den Themengebieten: <ul>
      <li> ";
        // line 93
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 94
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 95
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 96
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 97
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 98
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 99
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 100
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 101
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
      <li> ";
        // line 102
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["theme"]) ? $context["theme"] : null), "get", array()), "html", null, true);
        echo "</li>
    </ul></td>
  </tr>
  <tr>
    <td class=\"align-top\"> Bearbeitung von empfohlenen
      Mathematik-Lernmodulen</td>
    <td> <mark>";
        // line 108
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["modules"]) ? $context["modules"] : null), "done", array()), "html", null, true);
        echo "</mark> der empfohlenen Module
      bearbeitet aus den zehn <br> genannten Themengebieten</td>
  </tr>
</table>
</div>

<div class=\"table2\">
  <p><b> Studienvorbereitung - eMentoring </b> </p>
<table id=\"table4\">
  <tr>
    <td id=\"td10\"> Aktive Teilnahme an <br> Videokonferenzen </td>
    <td id=\"td11\"> Teilnahme an <mark> ";
        // line 119
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["conferences"]) ? $context["conferences"] : null), "participated", array()), "html", null, true);
        echo " </mark>
      von 5 Videokonferenzen</td>
  </tr>
  <tr>
    <td class=\"align-top\"> Bearbeitung von<br> Aufgaben zur <br>
      Vertiefung von <br> Inhalten des <br> Studienvorbereitungs- <br>programms</td>
    <td> Abgabe von <mark>";
        // line 125
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["homework"]) ? $context["homework"] : null), "done", array()), "html", null, true);
        echo "</mark> von 5 Hausaufgaben</td>
  </tr>
</table>
</div>
</body>
";
    }

    public function getTemplateName()
    {
        return "Teilnahmebescheinigung.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  211 => 125,  202 => 119,  188 => 108,  179 => 102,  175 => 101,  171 => 100,  167 => 99,  163 => 98,  159 => 97,  155 => 96,  151 => 95,  147 => 94,  143 => 93,  139 => 92,  126 => 82,  94 => 53,  90 => 52,  86 => 51,  78 => 46,  57 => 28,  40 => 14,  36 => 13,  29 => 9,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "Teilnahmebescheinigung.html", "/var/www/ilias/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/Teilnahmebescheinigung.html");
    }
}
