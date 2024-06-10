###[DEF]###
[name	=Ton-URL]

[folderid=164]
[xsize	=100]
[ysize	=50]

[var1	=0]

[flagText		=1]
[flagKo1		=1]	
[flagKo2		=0]
[flagKo3		=1]
[flagPage		=0]
[flagCmd		=0]
[flagDesign		=0]
[flagDynDesign	=1]

[captionText	=URL]

[flagSound		=1]
###[/DEF]###


###[PROPERTIES]###
[columns=100]
[row]
	[var1 = select,1,'Wiedergabe','0#einmalig abspielen|1#endlos wiederholen']
###[/PROPERTIES]###


###[EDITOR.JS]###
VSE_VSEID=function(elementId,obj,meta,property,isPreview,koValue) {
	if (isPreview) {
		var n="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'><tr><td><span class='app2_pseudoElement'>(UNSICHTBAR)</span></td></tr></table>";
	} else {
		var n="<table cellpadding='0' cellspacing='0' width='100%' height='100%' border='0'><tr><td><span class='app2_pseudoElement'>TON-URL</span></td></tr></table>";
	}
	obj.innerHTML=n;

	//Text immer zentrieren, kein Padding
	obj.style.textAlign="center";
	obj.style.padding="0";
	
	return false;
}

###[/EDITOR.JS]###


###[VISU.JS]###
VSE_VSEID_CONSTRUCT=function(elementId,obj) {
	//unsichtbares Visuelement
}

VSE_VSEID_REFRESH=function(elementId,obj,isInit,isRefresh,isLive,isActive,koValue) {
	//VE ist immer unsichtbar
	obj.style.display="none";

	if (isInit || isRefresh) {
		var n=visuElement_parseString(visuElement_getText(elementId),koValue);
		if (n=="STOP") {
			visuSoundStop();
		} else {
			visuSoundPlay(n,((obj.dataset.var1==1)?true:false));
		}
	}
}
###[/VISU.JS]###


###[HELP]###
Das Visuelement "Ton-URL" spielt eine beliebige Ton-URL ab. Die abzuspielende Ton-Datei muss per HTTP erreichbar sein und das Dateiformat muss vom Browser unterstützt werden (z.B. mp3).

<b>Hinweis:</b>
Dieses Visuelement ist in der Visualisierung nicht sichtbar, daher werden sämtliche Designeigenschaften mit Ausnahme der Eigenschaft "Beschriftung" (s.u.) ignoriert.


<h2>Spezifische Eigenschaften</h2>
Für weitere Einstellungen und Optionen siehe: <link>Allgemeine Informationen zu Visuelementen***1002</link>

<ul>
	<li>
		Wiedergabe: legt fest, ob die Ton-Datei nur einmalig oder immer wieder (endlos) abgespielt werden soll
		<ul>
			<li>Hinweis: es kann stets nur ein Ton zur gleichen Zeit wiedergegeben werden, daher wird die Wiedergabe ggf. abgebrochen sobald eine weitere Ton-URL abgespielt werden soll</li>
		</ul>
	</li>
</ul>


<h2>Kommunikationsobjekte</h2>
Dieses Visuelement kann (optional) folgende Kommunikationsobjekte (KO) verwalten:

<ul>
	<li>
		KO1: Steuerung
		<ul>
			<li>dieser KO-Wert wird zur <span style="background:#e0ffe0;">Steuerung und Beschriftung</span> verwendet (Designs, Funktionen und Formeln)</li>
			<li>immer wenn das KO auf einen Wert gesetzt wird, wird die entsprechende Ton-URL ("Beschriftung") abgespielt</li>
		</ul>
	</li>

	<li>
		KO3: Steuerung des dynamischen Designs
		<ul>
			<li>dieser KO-Wert wird ausschließlich zur Steuerung eines <link>dynamischen Designs***1003</link> verwendet</li>
			<li>wenn dieses KO angegeben wurde, wird ein dynamisches Design durch dieses <i>KO3</i> gesteuert</li>
			<li>wenn dieses KO nicht angegeben wurde, wird ein dynamisches Design durch das <i>KO1</i> gesteuert</li>
		</ul>
	</li>
</ul>


<h2>Besonderheiten</h2>
<ul>
	<li>
		Verhalten des Visuelements:
		<ul>
			<li>wird im Feld "Beschriftung" des Basis-Designs bereits eine URL angegeben, wird der Ton unmittelbar abgespielt sobald die entsprechende Visuseite angezeigt wird</li>
			<li>die Wiedergabe läuft auch bei einem Seitenwechsel weiter</li>
			<li>wird die Visuseite, die das Visuelement enthält erneut aufgerufen, beginnt die Wiedergabe von vorn</li>
			<li>wird ein dynamisches Design verwendet, wird die Wiedergabe des entsprechenden Tons bei jedem Setzen des KOs neugestartet</li>
			<li>der <link>Befehl "Ton abspielen"***1007</link> (z.B. Logik) beendet die aktuelle Wiedergabe und wird dann ausgeführt (Töne werden also nicht "gemischt")</li>
		</ul>
	</li>

	<li>Im Feld "Beschriftung" (auch in dynamischen Designs) muss die URL der Tondatei angegeben werden.</li>
	<li>die Wiedergabe kann mit der Angabe "STOP" (Grossbuchstaben!) in der Beschriftung jederzeit beendet werden</li>
	<li>Designs: alle Designeigenschaften mit Ausnahme von "Beschriftung" werden ignoriert (das Visuelement ist in der Visualisierung nicht sichtbar)</li>
	<li>Seitensteuerung/Befehle stehen nicht zu Verfügung</li>
</ul>

<b>Wichtig:</b>
<link>Konfigurierte Töne***1000-29</link> stehen für dieses Visuelement nur dann (als lokale URL) zu Verfügung, wenn ein konfigurierter Ton in einer Visualisierung tatsächlich genutzt wird (<link>Befehle***1007</link>): Ungenutzte Ton-Dateien werden bei der <link>Projektaktivierung***103-13</link> nicht übertragen.


<h2>Bedienung in der Visualisierung</h2>
In der Visualisierung ist dieses Element vollständig unsichtbar und daher nicht bedienbar. Die Steuerung erfolgt ausschließlich über den KO-Wert des zugewiesenen KOs.

<b>Wichtig:</b>
Auf einigen Endgeräten (z.B. iOS-basierten Geräten) ist unter Umständen die Tonausgabe und/oder die Sprachausgabe erst dann verfügbar, wenn diese einmalig mit einem Klick (Nutzerinteraktion) aktiviert wurde. In diesem Fall wird am oberen Bildschirmrand die Meldung "Tonausgabe aktivieren" angezeigt und sollte mit einem Klick bestätigt werden (siehe auch <link>Visualisierung***b-0</link>).
###[/HELP]###


