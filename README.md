NZJuiAutoCompleteTarget
=======================

DaJuiAutoCompleteTarget class file for Yii Framework

Questo widget serve per creare un campo JuiAutoComplete e relativo campo di riempimento.
Un esempio: popolare il campo user_id cercando l'utente per nome.

al classico CJuiAutoComplete sono state aggiunte delle proprietà:

obbligatorie:
```php
'sourceField' => 'user_nome', // Nome del campo autocomplete (va a sostituire la proprietà 'name' di CJuiAutoComplete)
'targetField' => 'user_id', // Nome del campo di destinazione, in genere il nome della proprietà del modello
 ```
Opzionali:
```php
'multiSource' => array (
       'primary' => array('/utenti_uomini/jsonList'), // Sorgente primaria
       'secondary' => array('/utenti_donne/jsonList'), // Sorgente secondaria
       'label' => 'Cerca tra le donne', // Label dello switch tra le due sorgenti, indicherà la seconda sorgente
       'name' => 'checkbox_switch', // nome del checkbox che fara lo switch
   ),
```
Impostando il parametro multiSource viene creato un checkbox per switchare tra le due sorgenti impostate

esempio

```html
<div class="row">
        <?php echo $form->labelEx($model,'agent_id'); ?>
        <?php $this->widget('DaJuiAutoCompleteTarget', array(
                'model' => $model,
                'sourceField' => 'user_autocomplete',
                'targetField' => 'user_id',
                'value' => $model->getUserValue(),
                'sourceUrl'=>array('api/1/user/jsonList'),
                'options'=>array(
                    'minLength'=>'3',
                ),
            ));
        ?>
        <?php echo $form->error($model,'agent_id'); ?>
</div>
```
