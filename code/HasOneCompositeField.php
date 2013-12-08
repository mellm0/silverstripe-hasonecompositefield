<?php
/**
 * Milkyway Multimedia
 * HasOneCompositeField.php
 *
 * A compositefield that saves the containing fields
 * into a has_one relationship
 *
 * @todo No deletion of object supported...
 * @todo Saving has_many and many_many not tested...
 *
 * @package milkyway/silverstripe-hasonecompositefield
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class HasOneCompositeField extends CompositeField {
	/**
	 * @var DataObjectInterface
	 */
	protected $record;

	/**
	 * @var array
	 */
	protected $extraData = array();

	public function __construct($name, $title = null, $record, FieldList $fields = null) {
		$this->name = $name;
		$this->title = $title;
		$this->record = $record;

		if(!$fields) {
			if($this->record->hasMethod('getHasOneFields'))
				$fields = $this->record->getHasOneFields($name);
			else
				$fields = $this->record->getCMSFields()->dataFields();
		}

		parent::__construct($fields);
	}

	public function setExtraData($data = array()) {
		$this->extraData = $data;
		return $this;
	}

	public function getExtraData() {
		return $this->extraData;
	}

	public function hasData() {
		return true;
	}

	public function isComposite() {
		return false;
	}

	public function saveInto(DataObjectInterface $record) {
		if($this->record) {
			// HACK: Use a fake Form object to save data into fields
			$form = new Form($this->record, $this->name . '-form', $this->FieldList(false), new FieldList());
			$form->loadDataFrom($this->value);
			$form->saveInto($this->record);

			if(count($this->extraData))
				$this->record->castedUpdate($this->extraData);

			$this->record->write();

			$fieldName = substr($this->name, -2) == 'ID' ? $this->name : $this->name . 'ID';
			$record->$fieldName = $this->record->ID;

			unset($form);
		}
	}

	public function FieldList($prependName = true) {
		$fields = parent::FieldList();

		if($fields && $fields->exists()) {
			if($this->value && (is_array($this->value) || ($this->value instanceof DataObjectInterface)))
				$value = $this->value;
			else
				$value = $this->record;

			if($value) {
				// HACK: Use a fake Form object to save data into fields
				$form = new Form($this->record, $this->name . '-form', $fields, new FieldList());
				$form->loadDataFrom($value);
				$fields->setForm($this->form);
				unset($form);
			}

			if($prependName)
				$this->prependName($fields);
		}

		return $fields;
	}

	protected function prependName(FieldList $fields) {
		foreach($fields as $field){
			if($field->isComposite())
				$this->prependName($field->FieldList());

			$field->setName($this->name . '[' . $field->Name . ']');
		}
	}
} 