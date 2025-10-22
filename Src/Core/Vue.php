<?php
/**
 * Classe de la vue
 *
 * @version 6.202012 - Ajout de l'attribut langue de la page
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2020, www.DavidSENSOLI.com
 */

namespace Appy\Src\Core;

/**
 * @brief CLASSE DE VUE HTML
 *
 * @version 6.2009 - Suppression de la gestion des fichiers .JS
 * @version 5.1905 - Possibilité de changer de gabarit pour une page spécifique
 * @version 5.1901 - Passage de la classe Session dans Core
 * @version 4.1710 - namespace Appy\Core
 * @version 3.1710 - Ajout du debug entre la balise <html>
 * @version 2.1701 - Ajout des balises meta Keywords et description
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2019, www.DavidSENSOLI.com
 */
class Vue
{
    /**
     * Nom du fichier associé à la vue
     * @var String $fichier
     */
    private $fichier;

    /**
     * Titre de la vue (défini dans le fichier vue)
     * @var String $titre
     */
    private $titre;

    /**
     * Contenu de la balise meta keywords
     *
     * @var String $keywords
     */
    private $keywords;

    /**
     * Contenu de la balise meta description
     *
     * @var String $keywords
     */
    private $description;

    /**
     * Liste des CSS à inclure
     * @var Array $css
     */
    private $css;

    public function __construct($action)
    {
        /** Détermination du nom du fichier vue à partir de l'action */
        $this->fichier = $action.'.tpl';
    }

    /**
     * Affiche la vue compressée en utilisant un gabarit (gabarit.tpl si non précisé)
     *
     * @param Array $donnees
     */
    public function generer($donnees, $gabarit = 'gabarit')
    {
        // Génération de la partie spécifique de la vue
        $contenu = $this->genererFichier($this->fichier, $donnees);

        // Génération du gabarit commun utilisant la partie spécifique
        $vue = $this->genererFichier(BASE_PATH.'vues'.DS.$gabarit.'.tpl', array(
            'contenu'     => $contenu,
            'css'         => $this->css,
            'description' => $this->description,
            'keywords'    => $this->keywords,
            'titre'       => $this->titre
            )
        );

        // Renvoi de la vue au navigateur
        echo $vue;
    }

    /**
     * Génère un fichier vue et renvoie le résultat produit
     *
     * @param String $fichier
     * @param Array $donnees
     * @return Vue
     * @throws Exception
     */
    private function genererFichier($fichier, $donnees)
    {
        if (file_exists($fichier)) {

            // Rend les éléments du tableau $donnees accessibles dans la vue
            extract($donnees);

            // Démarrage de la temporisation de sortie
            if (!in_array('ob_gzhandler', ob_list_handlers())) {
                ob_start('ob_gzhandler');
            } else {
                ob_start();
            }

            // Inclut le fichier vue, son résultat est placé dans le tampon de sortie
            require $fichier;

            // Arrêt de la temporisation et renvoi du tampon de sortie
            return ob_get_clean();
        } else {
            throw new \Exception("La vue ne trouve pas '$fichier' !");
        }
    }
}
