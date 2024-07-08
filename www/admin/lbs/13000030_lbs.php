###[DEF]###
[name        =SendByChange            ]
[titel        =SBC                    ]

[e#1 TRIGGER=                        ]

[a#1        =                        ]

[v#1        =                        ] Vergleichswert
###[/DEF]###


###[HELP]###
Dieser Baustein setzt A1 auf den Wert an E1, wenn der Wert an E1 verändert wurde.
Der Vergleich ist dabei "hart", z.B. ist 00&ne;0 (dies entspricht also einer Änderung des Wertes).

<b>Wichtig:</b>
Das erste Telegramm (nach einem EDOMI-Neustart) an E1 wird immer(!) an A1 übergeben, da dies stets einer Änderung des Wertes entsprechen wird.
Selbst wenn das Telegramm dem Initialwert entspricht, wird A1 auf E1 gesetzt (A1 wird beim ersten Start des Bausteins auf den Initialwert gesetzt).

E1: Signal
A1: bei Änderung Wert von E1 (auch Initialwert von E1)
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{

    if ($E = logic_getInputs($id)) {

        $V1 = logic_getVar($id, 1);
        if ($E[1]['refresh'] == 1 && (string)$E[1]['value'] !== (string)$V1) {
            logic_setVar($id, 1, $E[1]['value']);
            logic_setOutput($id, 1, $E[1]['value']);
        }

    }
}

?>
###[/LBS]###


###[EXEC]###
<?

?>
###[/EXEC]###
