<?php
namespace Drupal\formtesting\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;

class EditTestingForm extends FormBase {
  /**
   * {@inheritdoc}
   */
    public function getFormId() {
      return 'edit_formtesting_id';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

      $id = \Drupal:: routeMatch()->getParameter('id');
      $query = \Drupal::database();
      $data = $query->select('formtesting','f')
                      ->fields('f',['formtesting_id','name','email','phone','technology','address','gender','hobby'])
                      ->condition('f.formtesting_id',$id,'=')
                      ->execute()->fetchAll(\PDO::FETCH_OBJ);

      //print_r($data);

      $form['name'] = array(
        '#type' => 'textfield',
        '#title' => t('Enter Name:'),
        '#default_value'=> $data[0]->name
      );
      $form['email'] = array(
        '#type' => 'textfield',
        '#title' => t('Enter Email ID:'),
        '#default_value'=> $data[0]->email
      );
      $form['phone'] = array (
        '#type' => 'tel',
        '#title' => t('Enter Contact Number'),
        '#default_value'=> $data[0]->phone
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
        '#default_value'=> $data[0]->technology
      );
      $form['gender'] = array(
        '#type' => 'radios',
        '#title' => t('Select Gender'),
        /*'#description' => t('Select a method for deleting annotations.'),*/
        '#options' => array('Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'),
        '#default_value'=> $data[0]->gender
      );
      $newhobby = array_filter(unserialize($data[0]->hobby));
      //print_r($newhobby);
      $form['hobby'] = array(
        '#type' => 'checkboxes',
        '#title' => $this->t('Select Hobby'),
        '#options' => array('Dancing' => 'Dancing', 'Singing' => 'Singing', 'Painting' => 'Painting'),
        '#default_value' => array($newhobby['Dancing'], $newhobby['Singing'], $newhobby['Painting']),
        //'#attributes' => array('checked' => 'checked')
      );
      $form['address'] = array (
        '#type' => 'textarea',
        '#title' => t('Enter Address'),
        '#default_value'=> $data[0]->address
      );
      $form['actions']['#type'] = 'actions';
      $form['actions']['update'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Update'),
        '#button_type' => 'primary',
      );
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
        $form_state->setErrorByName('phone', $this->t('Please enter a valid Contact Number'));
      }
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
      
      $id = \Drupal:: routeMatch()->getParameter('id');
      $field = $form_state->getValues();

      $conn = Database::getConnection();
      $fields["name"] = $field['name'];
      $fields["email"] = $field['email'];
      $fields["phone"] = $field['phone'];
      $fields["technology"] = $field['technology'];
      $fields["address"] = $field['address'];
      $fields["gender"] = $field['gender'];
      $fields["hobby"] = serialize(array_filter($field['hobby']));
      
        $conn->update('formtesting')
             ->fields($fields)
             ->condition('formtesting_id',$id)
             ->execute();

        $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../view_data');
        $response->send();
        
        \Drupal::messenger()->addMessage($this->t('The data has been updated succesfully.'));
          
          //$form_state->setRedirect('view_data');

      //remove the unwanted keys from $field
      /*unset($field['submit'],$field['form_build_id'],$field['form_token'],$field['form_id'],$field['op']);

      $query = \Drupal::database();
      $query->insert('formtesting')->fields($field)->execute();
      \Drupal::messenger()->addMessage($this->t('The data has been succesfully saved'),'status',TRUE);*/
    }
}