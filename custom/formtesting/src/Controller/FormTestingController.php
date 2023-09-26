<?php
namespace Drupal\formtesting\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class FormTestingController extends ControllerBase {
    public function view_data() {

        /*$form = \Drupal::formBuilder()->getForm('Drupal\formtesting\Form\FormTestingForm');
        return array(
            '#formtesting_id' => $form
        );*/
    
        $limit = 5;
        $database = \Drupal::database();
        /* method 1
        $query = $database->query("SELECT * FROM {formtesting}");
        $result = $query->fetchAll();   */

        $result = $database->select('formtesting','f')
                           ->fields('f',['formtesting_id','name','email','phone','technology','address','gender','hobby'])
                           ->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit)
                           ->orderBy('formtesting_id')
                           ->execute()->fetchAll(\PDO::FETCH_OBJ);
        
        $data = [];
        $count = 0;

        //pagination
        $params = \Drupal::request()->query->all();
        //print_r($params);
        //exit;

        if(empty($params) || $params['page']==0){
            $count = 1;
        }
        elseif($params['page']==1){
            $count = $params['page'] + $limit;
        }
        else{
            $count = $params['page'] * $limit;
            $count++;
        }

        foreach($result as $row){
            //$array = array_filter($array, function($a) { return ($a !== 0); });
            $data[] = [
              'serial_no' => $count.".",
              'name' => $row->name,
              'email' => $row->email,
              'phone' => $row->phone,
              'gender' => $row->gender,
              'technology' => $row->technology,
              'hobby' => implode(', ', unserialize($row->hobby)),
              'address' => $row->address,
              'edit' => t("<a href='edit_data/$row->formtesting_id'>Edit</a>"),
              'delete' => t("<a href='delete_data/$row->formtesting_id'>Delete</a>")
            ];
            $count++;
        }

        $header = array('Sr. No.','Name','Email','Phone','Gender','Technology','Hobby','Address','Edit','Delete');

        $build['table'] = [
          '#type'=>'table',
          '#header'=>$header,
          '#rows'=>$data
        ];

        $build['pager'] = [
          '#type'=>'pager',
        ];

        return [
          $build,
          // '#theme' => 'formDetails',
          '#title'=> 'View Form Details',
        
          // '#form_Details' => 'test variable',
          // '#theme' => 'form_Details'
        ];
    }

    public function delete_data($id){
      $query = \Drupal::database();
      $query->delete('formtesting')
            ->condition('formtesting_id',$id,'=')
            ->execute();

      $response = new \Symfony\Component\HttpFoundation\RedirectResponse('../view_data');
      $response->send();
            
      \Drupal::messenger()->addMessage($this->t('The data has been deleted succesfully.'));
    }
}