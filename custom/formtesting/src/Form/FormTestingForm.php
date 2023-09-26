<?php
namespace Drupal\formtesting\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use \Drupal\Core\includes\bootstrap;

class FormTestingForm extends FormBase {
  /**
   * {@inheritdoc}
   */
    public function getFormId() {
      return 'formtesting_id';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
      $form['name'] = array(
        '#type' => 'textfield',
        '#title' => t('Enter Name:'),
      );
      $form['email'] = array(
        '#type' => 'email',
        '#title' => t('Enter Email ID:'),
      );
      $form['phone'] = array (
        '#type' => 'tel',
        '#title' => t('Enter Contact Number'),
      );
      $form['technology'] = array (
        '#type' => 'select',
        '#title' => $this->t('Select technology'),
        '#options' => [
          //'' => $this->t('--Select technology--'),
          'PHP' => $this->t('PHP'),
          'Python' => $this->t('Python'),
          'Java' => $this->t('Java'),
        ],
      );
      $form['gender'] = array(
        '#type' => 'radios',
        '#title' => t('Select Gender'),
        /*'#description' => t('Select a method for deleting annotations.'),*/
        '#options' => array('Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'),
        '#default_value' => 'Male',
      );
      $form['hobby'] = array(
        '#type' => 'checkboxes',
        '#title' => $this->t('Select Hobby'),
        '#options' => array('Dancing' => 'Dancing', 'Singing' => 'Singing', 'Painting' => 'Painting'),
        //'#default_value' => array('Dancing'),
      );
      $form['address'] = array (
        '#type' => 'textarea',
        '#title' => t('Enter Address'),
      );
      $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        '#button_type' => 'primary',
      );
      $form['#theme'] = 'event_templ';
      return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
      if(!$form_state->getValue('name') || empty($form_state->getValue('name'))) {
        $form_state->setErrorByName('name', $this->t('Please enter your Name'));
      }

      if(!$form_state->getValue('email') || empty($form_state->getValue('email'))) {
        $form_state->setErrorByName('email', $this->t('Please enter your Email'));
      }

      $email1=$form_state->getValue('email');
      if (!preg_match("/^[a-z0-9_-]+[a-z0-9_.-]*@[a-z0-9_-]+[a-z0-9_.-]*\.[a-z]{2,5}$/",trim($email1))) {
        $form_state->setErrorByName('email', $this->t('Please enter valid Email'));
      } 
      
      //echo strlen($form_state->getValue('phone'));
    
      if(strlen($form_state->getValue('phone')) != 10) {
        $form_state->setErrorByName('phone', $this->t('Please enter a valid Contact Number that should have 10 digits'));
      }
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
      
      $field = $form_state->getValues();

      $conn = Database::getConnection();
      $fields["name"] = $field['name'];
      $fields["email"] = $field['email'];
      $fields["phone"] = $field['phone'];
      $fields["technology"] = $field['technology'];
      $fields["address"] = $field['address'];
      $fields["gender"] = $field['gender'];
      $fields["hobby"] = serialize(array_filter($field['hobby']));
      
        $conn->insert('formtesting')
          ->fields($fields)->execute();
          $url = Url::fromRoute('formtesting.view_data');
          $form_state->setRedirectUrl($url);
        // $response = new \Symfony\Component\HttpFoundation\RedirectResponse('view_data');
        // $response->send();
          
        \Drupal::messenger()->addMessage($this->t('The data has been saved succesfully.'));       
 
$node = \Drupal::entityTypeManager()->getStorage('node')->create(['type' => 'formtesting', 'title' => 'Another node']);
// You can use the static create() method if you know the entity class.
$node = Node::create([
  'type' => 'formtesting',
  'title' => 'The node title',
  'field_address' => array(
    'value' => $form_state->getValue('address'),
    'format' => 'basic_html',
    ),
  'field_name' => array(
  'value' => $form_state->getValue('name'),
  'format' => 'basic_html',
  ),
  'formtesting_id' => $formtesting_id,
]);
$node->save();

  
    

    }
}