<?php
/**
 * Bake Template for Controller action generation.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Console.Templates.default.actions
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>

/**
 * <?php echo $admin; ?>paginate through the <?php echo $currentModelName; ?> records
 *
 * @return void
 */
	public function <?php echo $admin ?>index() {
		$this->Paginator->settings = array_merge($this->paginate, array(
		));
		$<?php echo $pluralName ?> = $this->paginate();
		$this->set(compact('<?php echo $pluralName ?>'));
	}

/**
 * <?php echo $admin; ?>view a single <?php echo $currentModelName; ?> record
 *
 * @param	string $id the primary key for the <?php echo $currentModelName; ?>
 * @return void
 * @throws NotFoundException If the passed id record does not exist
 */
	public function <?php echo $admin ?>view($id = null) {
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
		}
		$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
		$<?php echo $singularName; ?> = $this-><?php echo $currentModelName; ?>->find('first', $options);
		$this->set(compact('<?php echo $singularName; ?>'));
	}

<?php $compact = array(); ?>
/**
 * <?php echo $admin; ?>add a new <?php echo $currentModelName; ?> record
 *
 * @return void
 * @throws ValidationFailedJsonApiException If the add fails due to validation errors
 * @throws ModelSaveFailedJsonApiException If the add fails on save
 */
	public function <?php echo $admin ?>add() {
		$this-><?php echo $currentModelName; ?>->create();

		if (!empty($this->request->data) && $this-><?php echo $currentModelName; ?>->save($this->request->data)) {
			$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $this-><?php echo $currentModelName; ?>->id));
			$<?php echo $singularName; ?> = $this-><?php echo $currentModelName; ?>->find('first', $options);
			$this->set(compact('<?php echo $singularName; ?>'));
		} else {
			// if there are validation errors, render them, else return a generic
			// save failure exception
			$invalidFields = $this-><?php echo $currentModelName; ?>->invalidFields();
			if (!empty($invalidFields)) {
				throw new ValidationFailedJsonApiException(__('<?php echo $singularHumanName; ?> create failed.'), $invalidFields);
			}
			throw new ModelSaveFailedJsonApiException(__('<?php echo $singularHumanName; ?> create failed.'));
		}
	}

<?php $compact = array(); ?>
/**
 * <?php echo $admin; ?>edit a <?php echo $currentModelName; ?> record
 *
 * @param string $id the primary key for the <?php echo $currentModelName; ?> record
 * @return void
 * @throws NotFoundException If the passed id record does not exist
 * @throws ValidationFailedJsonApiException If the edit fails due to validation errors
 * @throws ModelSaveFailedJsonApiException If the edit fails on save
 */
	public function <?php echo $admin; ?>edit($id = null) {
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
		}
		
		$this-><?php echo $currentModelName; ?>->id = $id;

		if (!empty($this->request->data) && $this-><?php echo $currentModelName; ?>->save($this->request->data)) {
			$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
			$this->request->data = $<?php echo $singularName; ?> = $this-><?php echo $currentModelName; ?>->find('first', $options);
			$this->set(compact('<?php echo $singularName; ?>'));
		} else {
			// if there are validation errors, render them, else return a generic
			// save failure exception
			$invalidFields = $this-><?php echo $currentModelName; ?>->invalidFields();
			if (!empty($invalidFields)) {
				throw new ValidationFailedJsonApiException(__('<?php echo $singularHumanName; ?> edit failed.'), $invalidFields);
			}
			throw new ModelSaveFailedJsonApiException(__('<?php echo $singularHumanName; ?> edit failed.'));
		}
	}

/**
 * <?php echo $admin ?>delete a <?php echo $currentModelName; ?> record
 *
 * @param string $id the primary key for the <?php echo $currentModelName; ?>
 * @return CakeResponse
 * @throws NotFoundException If the passed id record does not exist
 * @throws ModelDeleteFailedJsonApiException If the delete fails
 */
	public function <?php echo $admin; ?>delete($id = null) {
		$this-><?php echo $currentModelName; ?>->id = $id;
		if (!$this-><?php echo $currentModelName; ?>->exists()) {
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
		}
		$this->request->onlyAllow('delete');
		if ($this-><?php echo $currentModelName; ?>->delete()) {
			$this->response->statusCode(204);
			return $this->response;
		} else {
			throw new ModelDeleteFailedJsonApiException(__('<?php echo $singularHumanName; ?> delete failed.'));
		}

	}
