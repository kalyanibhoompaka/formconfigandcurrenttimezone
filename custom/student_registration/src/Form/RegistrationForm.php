<?php
/**
 * @file
 * Contains \Drupal\student_registration\Form\RegistrationForm.
 */
namespace Drupal\student_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Symfony\Component\HttpFoundation\RedirectResponse;


class RegistrationForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'student_registration_form';
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $conn = Database::getConnection();
    $record = array();
   if (isset($_GET['num'])) {
       $query = $conn->select('student_registration', 'm')
           ->condition('id', $_GET['num'])
           ->fields('m');
       $record = $query->execute()->fetchAssoc();
   }
    $form['student_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter Name:'),
      '#required' => TRUE,
    );
    $form['student_rollno'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter Enrollment Number:'),
      '#required' => TRUE,
    );
    $form['student_mail'] = array(
      '#type' => 'email',
      '#title' => t('Enter Email ID:'),
      '#required' => TRUE,
    );
    $form['student_phone'] = array (
      '#type' => 'tel',
      '#title' => t('Enter Contact Number'),
    );
    $form['student_dob'] = array (
      '#type' => 'date',
      '#title' => t('Enter DOB:'),
      '#required' => TRUE,
    );
    $form['student_gender'] = array (
      '#type' => 'select',
      '#title' => ('Select Gender:'),
      '#default_value' => (isset($record['student_gender']) && $_GET['num']) ? $record['student_gender']:'',
      '#options' => array(
        'Male' => t('Male'),
		'Female' => t('Female'),
        'Other' => t('Other'),
      ),
    );
    $form['category'] = array(
      '#title' => t('category'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        '#include_anonymous'  => 'FALSE',
        '#target_bundles' => array('category'),
      ],
      '#description' => 'add category', 
    );
    $form['crust_size'] = array(
        '#title' => t('Crust Size'),
        '#type' => 'select',
        '#description' => 'Select the desired pizza crust size.',
        '#options' => array(t('--- SELECT ---'), t('10"'), t('12"'), t('16"')),
      );
      // $options = [];
      // $options[1] = 'example_url';
      // $options[2] = 'example_url4';
      // $options[3] = 'example_url2';
      
      $form['items_selected'] = [
        '#type' => 'checkboxes',
        '#options' =>array('blue' => $this->t('Blue'), 'red' => $this->t('Red')),
        '#title' => $this->t('Title'),
      ];

      $form['life_story'] = array(
        '#title' => t('Your Life Story'),
        '#type' => 'textarea',
      );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Register'),
      '#button_type' => 'primary',
    );




    $form['#theme'] = 'event_templ';
    return $form;



  }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if(strlen($form_state->getValue('student_rollno')) < 8) {
      $form_state->setErrorByName('student_rollno', $this->t('Please enter a valid Enrollment Number'));
    }
    if(strlen($form_state->getValue('student_phone')) < 10) {
      $form_state->setErrorByName('student_phone', $this->t('Please enter a valid Contact Number'));
    }
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state)  {
    try{
        $conn = Database::getConnection();       
        $field = $form_state->getValues();
        $student_name = $field['student_name'];
        $student_rollno = $field['student_rollno'];
        $student_mail = $field['student_mail'];
        $student_phone = $field['student_phone'];
        $student_dob = $field['student_dob'];
        $student_gender = $field['student_gender'];
        $crust_size = $field['crust_size'];
        $items_selected = serialize(array_filter($field['items_selected']));
        $life_story = $field['life_story'];

    
        // $arr = unserialize(array_filter($field['items_selected']));

    //  print_r($arr);
    if (isset($_GET['num'])) {
      $field  = array(
          'student_name'   => $student_name,
          'student_rollno' =>  $student_rollno,
          'student_mail' =>  $student_mail,
          'student_phone' => $student_phone,
          'student_dob' => $student_dob,
          'student_gender' => $student_gender,
          'crust_size' => $crust_size,
          'items_selected' => $items_selected,
          'life_story' => $life_story,

      );
      $query = \Drupal::database();
      $query->update('student_registration')
          ->fields($field)
          ->condition('id', $_GET['num'])
          ->execute();
          \Drupal::messenger()->addMessage(t("Student Registration Done!! Registered Values are:"));
          $form_state->setRedirect('student_registration.display_table_controller_display');
  }
   else
   {
       $field  = array(
        'student_name'   => $student_name,
        'student_rollno' =>  $student_rollno,
        'student_mail' =>  $student_mail,
        'student_phone' => $student_phone,
        'student_dob' => $student_dob,
        'student_gender' => $student_gender,
        'crust_size' => $crust_size,
        'items_selected' => $items_selected,
        'life_story' => $life_story,

      );
       $query = \Drupal::database();
       $query ->insert('student_registration')
           ->fields($field)
           ->execute();
           \Drupal::messenger()->addMessage(t("succesfully saved"));
           $url = Url::fromRoute('student_registration.display_table_controller_display');
           $form_state->setRedirectUrl($url);
      //  $response = new RedirectResponse("/student_registration/hello/table");
      //  $response->send();
   }

          // $conn->insert('student_registration')
          //       ->fields($fields)->execute();

                // \Drupal::messenger()->addMessage(t("Student Registration Done!! Registered Values are:"));
    

	foreach ($form_state->getValues() as $key => $value) {
        \Drupal::messenger()->addMessage($key . ': ' . $value);}  
        
        
    } catch(Exception $ex){
        \Drupal::logger('book_enquiry')->error($ex->getMessage());
    }
  }
}





