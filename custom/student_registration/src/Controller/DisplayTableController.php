<?php
namespace Drupal\student_registration\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use \Drupal\Core\Link;

/**
 * Class DisplayTableController.
 *
 * @package Drupal\student_registration\Controller
 */
class DisplayTableController extends ControllerBase {

     public function getContent() {
    // First we'll tell the user what's going on. This content can be found
    // in the twig template file: templates/description.html.twig.
    // @todo: Set up links to create nodes and point to devel module.
    $build = [
      'description' => [
        '#theme' => 'student_registration',
        '#description' => 'foo',
        '#attributes' => [],
      ],
    ];
    return $build;
  }

  /**
   * Display.
   *
   * @return string
   *   Return Hello string.
   */
  public function display() {
    /**return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: display with parameter(s): $name'),
    ];*/
    //create table header
    $header_table = array(
     'id'=>    t('SrNo'),
      'student_name' => t('student_name'),
        'student_rollno' => t('student_rollno'),
        'student_mail'=>t('student_mail'),
        'student_phone' => t('student_phone'),
        'student_dob' => t('student_dob'),
        'student_gender' => t('student_gender'),
        'crust_size' => t('crust_size'),
        'items_selected' => t('items_selected'),
        'life_story' => t('life_story'),
        'opt' => t('operations'),
        'opt1' => t('operations'),
  
    );

//select records from table
    $query = \Drupal::database()->select('student_registration', 'm');
      $query->fields('m', ['id','student_name','student_rollno','student_mail','student_phone','student_dob','student_gender','crust_size','items_selected','life_story']);
      $results = $query->execute()->fetchAll();
        $rows=array();
    foreach($results as $data){

        $delete = Url::fromUserInput('/student_registration/form/delete/'.$data->id);
        $edit   = Url::fromUserInput('/student_registration/form/student_registration?num='.$data->id);

      //print the data from table

             $rows[] = array(
            'id' =>$data->id,
                'student_name' => $data->student_name,
                'student_rollno' => $data->student_rollno,
                'student_mail' => $data->student_mail,
                'student_phone' => $data->student_phone,
                'student_dob' => $data->student_dob,
                'student_gender' => $data->student_gender,
                'crust_size' => $data->crust_size,
                'items_selected' => $data->items_selected,
                'life_story' => $data->life_story,
                'delete' => "<a href=''/student_registration/form/delete/'.$data->id'>delete</a>",


                // 'delete' => t("<a href='delete/$data->id'>Delete</a>")
                'delete' =>$delete
                //  Url::fromRoute('student_registration.delete_form');
//  Link::fromTextAndUrl(t('Book admin'), $delete)->toString();
                //  \Drupal::l('Delete', $delete),
                //  \Drupal::l('Edit', $edit),
            );
}
    //display data in site
    $form['table'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No users found'),
        ];
        return $form;
}}