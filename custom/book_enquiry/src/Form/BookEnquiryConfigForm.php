<?php
namespace Drupal\book_enquiry\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\book_enquiry\Form\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;


class BookEnquiryConfigForm extends ConfigFormBase {

    protected function getEditableConfigNames() {
        return ['book_enquiry.settings'];
    }
     
    public function getFormId() {
        return 'book_enquiry_settings';
    }
     
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('book_enquiry.settings');
    
        $form['Country'] = [
            '#default_value' => $config->get('Country'),
            '#title' => $this->t('Country:'),
            '#type' => 'textfield',
        ];
        
        $form['State'] = [
            '#default_value' => $config->get('State'),
            '#title' => $this->t('State:'),
            '#type' => 'textfield',
        ];
        
        $form['City'] = [
            '#default_value' => $config->get('City'),
            '#title' => $this->t('City:'),
            '#type' => 'textfield',
        ];
        $form['Timezone'] = array (
            '#default_value' => $config->get('Timezone'),
            '#type' => 'select',
            '#options' => [
              '' => $this->t('--Select Timezone--'),
              'America/Chicago' => $this->t('America/Chicago'),
              'Asia/Kolkata' => $this->t('Asia/Kolkata'),
              'Australia/Sydney' => $this->t('Australia/Sydney'),
            ],
            '#title' => $this->t('Timezone:'),
          );


        $form['Pincode'] = [
            '#default_value' => $config->get('Pincode'),
            '#title' => $this->t(' Pincode:'),
            '#type' => 'number',
        ];
          
        return parent::buildForm($form, $form_state);
    }
     
    public function submitForm(array &$form, FormStateInterface $form_state) {
    
        # save to config and clear cache
        $config = $this->config('book_enquiry.settings');
        $config
            ->set('Country', $form_state->getValue('Country'))
            ->set('State', $form_state->getValue('State'))
            ->set('City', $form_state->getValue('City'))
            ->set('Timezone', $form_state->getValue('Timezone'))
            ->set('Pincode', $form_state->getValue('Pincode'))
            ->save();
        
           

        // clear cache
        drupal_flush_all_caches();
        
        parent::submitForm($form, $form_state);



    }
}