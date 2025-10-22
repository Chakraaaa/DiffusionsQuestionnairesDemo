<?php

namespace Appy\Src;

class Jsonlog
{
    static private $json_file = 'json.log';

    static public function getLogHtml()
    {
        if (file_exists(self::$json_file)) {

            $json_source = file_get_contents(self::$json_file, true);
            $logs        = json_decode($json_source, true);

            $html = "";
            foreach ($logs as $log => $lignes) {
                $html .= "<p style='margin-top: 1vh;'>$log</p>";
                foreach ($lignes as $ligne) {
                    $html .= "<p style='margin-left: 1vw;'>$ligne</li>";
                }
            }

            return $html;
        } else {
            return "<p>Le fichier ".self::$json_file." est introuvable !</p>";
        }
    }

    static public function writeLog(string $key, string $ligne)
    {

        if (file_exists(self::$json_file)) {
            $json_source = file_get_contents(self::$json_file, true);
            $log         = json_decode($json_source, true);
        } else {
            $log["Fichier LOG"][] = "Créé le ".date("d/m/Y");
        }

        $log[$key][] = $ligne;

        file_put_contents(self::$json_file, json_encode($log, JSON_UNESCAPED_SLASHES), LOCK_EX);
    }
}
