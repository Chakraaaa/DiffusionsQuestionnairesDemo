<?php
/**
 * CLASSE DE LISTE DEROULANTE
 *
 * @version 3.1807 - Ajout du tableau d'arguments.
 * @version 3.1803 - Fin du tri par ordre alphabetique.
 * @version 3.1607 - Ajout du namespace Core.
 * @version 2.1501 -  Ajout de la ligne vierge à la fin de la liste en option.
 *
 * @author David SENSOLI
 * @copyright (c) 2018, www.DavidSENSOLI.com
 */

namespace Appy\Src;

class Select
{
    /**
     * name du select, sert pour l'id aussi
     * @var string $nom
     */
    private $nom;

    /**
     * Tableau des options (option value="$value">$key</option)
     * @var array $tab_options
     */
    private $tab_options = array();

    /**
     * Valeur sélectionnée de la liste
     * @var int $valeur_selectionnee
     */
    private $valeur_selectionnee;

    /**
     * Html renvoyé par la fonction
     * @var string $html
     */
    private $html;

    /**
     * Message d'erreur renvoyé par la classe
     * @var string $erreur
     */
    private $erreur;

    /**
     * Activation du submit
     * @var bool $submit
     */
    private $submit;

    /**
     * Ligne vierge a la fin de la liste déroulante
     * @var bool $ligne_vierge
     */
    private $ligne_vierge;

    /**
     * Tableau d'arguments
     * @var Array [$key => $valeur]
     */
    private $args;

    /**
     * Constructeur
     * @return void
     */
    public function __construct($nom, $tab_options, $valeur_selectionnee = NULL, $submit = NULL, $ligne_vierge = TRUE, $args = NULL)
    {
        if (empty($tab_options)) {
            $this->erreur = 'Le tableau des options est vide !';
        } else {
            $this->tab_options = $tab_options;
        }
        $this->nom                 = $nom;
        $this->valeur_selectionnee = $valeur_selectionnee;
        $this->args                = $args;
        $this->ligne_vierge        = $ligne_vierge;
    }

    /**
     * Renvoie l'html
     * @return string
     */
    public function __toString()
    {
        if (!empty($this->tab_options)) {

            $this->html = "<select id=\"$this->nom\" name=\"$this->nom\" ";

            if ($this->submit) {
                $this->html .= "onchange='this.form.submit();'";
            }

            if (is_array($this->args)) {
                foreach ($this->args as $key => $value) {
                    $this->html .= " $key='$value'";
                }
            }

            $this->html .= ">";
            if (strlen($this->valeur_selectionnee) > 0) {                                   // Si la liste a une valeur sélectionnée
                if (in_array($this->valeur_selectionnee, $this->tab_options)) {             // et que cette valeur existe dans le tableau des options
                    $this->html .= '<option value="'.$this->valeur_selectionnee.'">';
                    $this->html .= stripslashes(array_search($this->valeur_selectionnee, $this->tab_options)).'</option>'; // on crée la premiére ligne de la liste avec la valeur sélectionnée
                } elseif ($this->valeur_selectionnee == 0) {                                // Si la liste a une valeur sélectionnée vide
                    $this->html .= '<option value="0">&nbsp;</option>';                   // on retourne une ligne vierge
                } else {
                    $this->erreur = 'La valeur '.$this->valeur_selectionnee.'
                        n\'existe pas dans le tableau des options.';                      // sinon on affiche l'erreur
                }
            }
            foreach ($this->tab_options as $key => $valeur) {                               // Ensuite on crée les autres lignes de la liste avec le tableau des options
                if ($valeur != $this->valeur_selectionnee) {                                 // sauf si la valeur est déja sélectionnée
                    $this->html .= "<option value=\"$valeur\">$key</option>";
                }
            }

            if ($this->valeur_selectionnee != 0 AND $this->ligne_vierge == true) {         // Ajout d'une ligne vierge a la fin
                $this->html .= '<option value="0">&nbsp;</option>';
            }

            $this->html .= '</select>';
        }

        if (!empty($this->erreur)) {                                                        // S'il y a une erreur
            return ($this->erreur);                                                       // on renvoit l'erreur
        } else {
            return ($this->html);                                                         // sinon on renvoit la balise <select> correcte
        }
    }
}
