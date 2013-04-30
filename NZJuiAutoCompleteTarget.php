<?php

/**
 * DaJuiAutoCompleteTarget class file.
 *
 * @author Nicola Tomassoni <nicola@digisin.it>
 * @link http://www.digisin.it/
 * @copyright Copyright &copy; 2012 - Digisin soc. coop
 * @license http://da.digisin.it/license/
 */
/**
 * @desc
 * Questo widget serve per creare un campo JuiAutoComplete e relativo campo di riempimento.
 * Un esempio: popolare il campo user_id cercando l'utente per nome.
 * 
 * al classico CJuiAutoComplete sono state aggiunte delle proprietà:
 * 
 * obbligatorie:
 *      'sourceField' => 'user_nome', // Nome del campo autocomplete (va a sostituire la proprietà 'name' di CJuiAutoComplete)
 *      'targetField' => 'user_id', // Nome del campo di destinazione, in genere il nome della proprietà del modello
 * 
 * Opzionali:
 * 'multiSource' => array (
 *       'primary' => array('/utenti_uomini/jsonList'), // Sorgente primaria
 *       'secondary' => array('/utenti_donne/jsonList'), // Sorgente secondaria
 *       'label' => 'Cerca tra le donne', // Label dello switch tra le due sorgenti, indicherà la seconda sorgente
 *       'name' => 'checkbox_switch', // nome del checkbox che fara lo switch
 *   ),
 * 
 * Impostando il parametro multiSource viene creato un checkbox per switchare tra le due sorgenti impostate
 * 
 */
Yii::import('zii.widgets.jui.CJuiAutoComplete');

class NZJuiAutoCompleteTarget extends CJuiAutoComplete {

  var $sourceField = '';
  var $targetField = '';
  var $multiSource = array();

  public function run() {
    $output = '';

    $target = CHtml::activeName($this->model, $this->targetField);
    $source = CHtml::activeName($this->model, $this->sourceField);
    $sourceId = CHtml::getIdByName($source);
    $targetId = CHtml::getIdByName($target);
    // Source elaborata da CJuiAutoComplete
    $this->name = $source;
    // Campo hidden
    $output .= CHtml::activeHiddenField($this->model, $this->targetField);
    // Se impostato processo il multisource
    if (isset($this->multiSource['primary']) && isset($this->multiSource['secondary'])) {
      $sources = array(CHtml::normalizeUrl($this->multiSource['primary']), CHtml::normalizeUrl($this->multiSource['secondary']));
      $this->source = $sources[0];

      $switchName = isset($this->multiSource['name']) ? $this->multiSource['label'] : $sourceId . '_switch';
      $switchLabel = isset($this->multiSource['label']) ? $this->multiSource['label'] : 'Cambia sorgente';
      // Forms fields
      $output .= '<div class="autocomplete-switch">';
      $output .= CHtml::label($switchLabel, $switchName);
      $output .= CHtml::checkBox($switchName);
      $output .= '</div>';
      // Register script
      $this->JsSwitchSource($switchName, $sourceId, $sources);
    }
    // Funzioni per passare l'id del nome trovato al campo hidden
    $this->options['select'] = 'js:function(event, ui){ jQuery("#' . $targetId . '").val(ui.item["id"]); }';
    $this->options['search'] = 'js:function(event, ui) { jQuery("#' . $targetId . '").val(""); }';
    // Register script
    $this->JsAutocompleteSync($sourceId, $targetId);
    // Prima processo e stampo l'autocomplete
    parent::run();
    // Poi il mio output aggiuntivo
    echo $output;
  }

  /**
   * @desc Visualizza o nasconde la stringa di default e simula il blur del campo del modello per attivare la validazione ajax 
   * 
   * @param type $source autocomplete id
   * @param type $destination campo presente nel modello
   */
  private function JsAutocompleteSync($source, $destination) {
    Yii::app()->clientScript->registerScript($source, '
if($("#' . $source . '").val() == "Cerca...")
    $("#' . $source . '").css("color", "silver");
        
$("#' . $source . '").focusin(function() {
    if($(this).val() == "Cerca...") {
        $(this).val("");
        $("#' . $source . '").css("color", "black");
    }
});

$("#' . $source . '").focusout(function() {
    if(jQuery(this).val() == "") {
        $("#' . $destination . '").val("");
    }

    $("#' . $destination . '").blur();
});');
  }

  /**
   * @desc Cambia il source all'oggetto jQuery autocomplete
   * 
   * @param type $obj checkbox id
   * @param type $target autocomplete id
   * @param type $urls url di ricerca primaria e secondaria
   */
  private function JsSwitchSource($obj, $target, $urls) {
    Yii::app()->clientScript->registerScript('customer_source', '
$("#' . $obj . '").click(function(){
    if($(this).attr("checked")) {
        $("#' . $target . '").autocomplete("option", "source", "' . $urls[1] . '");
    }
    else {
        $("#' . $target . '").autocomplete("option", "source", "' . $urls[0] . '");
    }
});');
  }

}

?>
