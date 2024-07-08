###[DEF]###
[name        =Klemme 8-fach            ]
[titel        =Klemme                    ]

[e#1 TRIGGER=                        ]
[e#2 TRIGGER=                        ]
[e#3 TRIGGER=                        ]
[e#4 TRIGGER=                        ]
[e#5 TRIGGER=                        ]
[e#6 TRIGGER=                        ]
[e#7 TRIGGER=                        ]
[e#8 TRIGGER=                        ]

[a#1        =                    ]
###[/DEF]###


###[HELP]###
Dieser Baustein dient zum Beschalten eines(!) Baustein-Eingangs mit mehreren KOs oder Ausgängen.

Ein neues(!) Telegramm &ne;[leer] an einem Eingang wird 1:1 an A1 durchgereicht.
Treffen gleichzeitig Telegramme an verschiedenen Eingängen ein, wird der Ausgang stets auf den Wert des Eingangs mit der kleinsten ID gesetzt (z.B. hat E1 Priorität gegenüber E5).

Hinweis: Im Gegensatz zu einem Oder-Gatter wird nur der Eingang ausgewertet, an dem ein neues(!) Telegramm eingetroffen ist. Die Zustände der anderen Eingänge werden dabei ignoriert.

E1..E8: Signal
A1: Eingangswert
###[/HELP]###


###[LBS]###
<?
function LB_LBSID($id)
{
    if ($E = logic_getInputs($id)) {
        if (!isEmpty($E[8]['value']) && $E[8]['refresh'] == 1) {
            logic_setOutput($id, 1, $E[8]['value']);
        }
        if (!isEmpty($E[7]['value']) && $E[7]['refresh'] == 1) {
            logic_setOutput($id, 1, $E[7]['value']);
        }
        if (!isEmpty($E[6]['value']) && $E[6]['refresh'] == 1) {
            logic_setOutput($id, 1, $E[6]['value']);
        }
        if (!isEmpty($E[5]['value']) && $E[5]['refresh'] == 1) {
            logic_setOutput($id, 1, $E[5]['value']);
        }
        if (!isEmpty($E[4]['value']) && $E[4]['refresh'] == 1) {
            logic_setOutput($id, 1, $E[4]['value']);
        }
        if (!isEmpty($E[3]['value']) && $E[3]['refresh'] == 1) {
            logic_setOutput($id, 1, $E[3]['value']);
        }
        if (!isEmpty($E[2]['value']) && $E[2]['refresh'] == 1) {
            logic_setOutput($id, 1, $E[2]['value']);
        }
        if (!isEmpty($E[1]['value']) && $E[1]['refresh'] == 1) {
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
