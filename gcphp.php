<?php


class GCPHP {
  
  private $cfg;
  private $project_id;
  private $project_statuses;
  private $project_templates;
  private $debug;
  
  function __construct($config = 'config.json') {    
    
    $this->cfg = $this->loadConfig($config);
    $this->project_id = $this->cfg['project_id'];
    $this->debug = $this->cfg['debug'];
    
    if ($this->debug) {
      var_dump($this->cfg);
    }

    // load project statuses
    $params = ['account_id' => $this->cfg['account_id']];
    $r = $this->gogo_gathercontent('projects', $params);
    foreach ($r['data'] as $project) {
      if ($project['id'] == $this->project_id) {
        $this->project_statuses = $project['statuses']['data'];
      }
    }
    
    if ($this->debug) {
      var_dump($this->project_statuses);
    }
    
    $this->project_templates = [];
    
  }
    
      
  /**
    * get all items in this project
    */
  function getItems() {

    $params = ['project_id' => $this->project_id];
    $items = $this->gogo_gathercontent('items', $params);
    if ($items) return $items['data'];

  }


  /**
    * get all the workflow states
    */
  function getWorkflowStates() {

    return $this->project_statuses;

  }


  /**
    * get all templates
    */
  function getTemplates() {
    
    if (count($this->project_templates)) {
      return $this->project_templates;
    }

    $templates = [];

    $params = ['project_id' => $this->project_id];  
    $r = $this->gogo_gathercontent('templates', $params);  
    foreach ($r['data'] as $template) {
      $templates[$template['id']] = $template['name'];
    }
    
    if ($this->debug) {
      var_dump($templates);
    }
    
    $this->project_templates = $templates;
  
    return $templates;

  }
  
  /**
    * look up the template id based on a template name
    */
  function getTemplateID($template_name) {
    
    $templates = $this->getTemplates();
    $id = array_search($template_name, $templates);
    if ($id === FALSE) {
      return FALSE;
    }
    return $id;
    
  }


  /**
    * get a specific item
    * @param item id
    */
  function getItem($item_id) {

    if ($this->debug) {
      echo __FUNCTION__ . '(' . $item_id . ')' . chr(10);
    }
    
    $params = ['project_id' => $this->project_id];
    $item = $this->gogo_gathercontent('items/' . $item_id, $params);
    if (!$item) {
      echo 'ERROR: Could not find an item with that id.' . chr(10);
      return false;
    }
    
    if ($this->debug) {
      var_dump($item);
    }
    
    return $item['data'];

  }
  
  
  function getItemName($item) {
    if (!$item) return '';
    return $item['name'];
  }


  /**
    * item['status'] is an array
    * i don't know why since it looks like it only ever has one thing
    */
  function getItemStatus($item) {
    if (!$item) return '';
    
    if ($this->debug) {
      var_dump($item['status']);
    }
    
    $item_status = '';
    foreach ($item['status'] as $s) {
      $item_status = $s['name'];
    }
    
    return $item_status;
    
  }


  /**
    * tell me what template this item is using
    * if you have already called getTemplates, pass that as the second arg
    */
  function getItemTemplate($item, $templates = []) {
    if (!$item) return '';
    
    if (count($templates) == 0) {
      $templates = $this->getTemplates();
    }
    
    $template_id = $item['template_id'];
    if ($this->debug) {
      var_dump($template_id);
    }
    
    if ($template_id && array_key_exists($template_id, $templates)) {
      return $templates[$template_id];
    }
    
    return '';
    
  }
  
  
  /**
    * give me the value of this field
    */
  function getItemValue($item, $field_name, $field = 'value') {
    if (!$item) return '';
    
    $value = FALSE;
    
    // every config is a "tab" in the template
    $configs = array_keys($item['config']);
    
    foreach ($configs as $c) {
      
      // every element is a "field" on a tab
      $elements = $item['config'][$c]['elements'];
      foreach ($elements as $e) {
        if ($e['label'] == $field_name) {
          return $e[$field];
        }
      }
      
    }
    
    return $value;
    
  }
  
  
  /**
    * get all the files for an item id
    */
  function getItemFiles($item_id) {
    
    if ($this->debug) {
      echo __FUNCTION__ . '(' . $item_id . ')' . chr(10);
    }
    
    $params = ['project_id' => $this->project_id];
    $files = $this->gogo_gathercontent('items/' . $item_id . '/files', $params);
    
    if ($this->debug) {
      var_dump($files);
    }
    
    return $files['data'];
    
  }
  
  
  /**
    * get the url for an uploaded file
    * NOTE there may be more than one file in this field
    * so you might get a string or you might get an array
    */
  function getItemFile($item, $field_name, $files = FALSE) {

    if ($this->debug) {
      echo __FUNCTION__ . '(' . $item['id'] . ')' . chr(10);
    }
    
    $found = FALSE;
    $item_file = [];
    
    // if you didn't pass files
    if (!$files) $files = $this->getItemFiles($item['id']);
    
    // we have to map the name/label to an id
    $field_id = $this->getItemValue($item, $field_name, 'name');
    if ($field_id) {
      foreach ($files as $f) {
        // then, if this file field matches that id
        if ($f['field'] == $field_id) {
          $found = TRUE;
          $item_file[] = $f['url'];
        }
      }
    }

    if ($found) {
      if (count($item_file) == 1) {
        return current($item_file);
      } else if (count($item_file) > 1) {
        return $item_file;
      }
    }
    
    return FALSE;
    
  }
  
  
  function stripContent($content) {
    return trim(strip_tags($content));
  }
  
  
  function cleanContent($content) {

    $clean = $content;
    
    $clean = str_replace('<br> ', '<br>', $clean);

    $clean = str_replace('<br></p>', '</p>', $clean);
    $clean = str_replace('<br></p>', '</p>', $clean);
    $clean = str_replace('<br></p>', '</p>', $clean);

    $clean = preg_replace('/<p>\s*<\/p>/', '', $clean);

    $clean = str_replace('</p>', '</p>' . chr(10), $clean);

    return $clean;
    
  }
  
  
  function loadConfig($config) {

    if (!file_exists($config)) {
      echo 'ERROR: No config file: ' . $config . chr(10);
      exit;
    }

    return json_decode(file_get_contents($config), TRUE);
    
  }
  
  /**
    * you probably shouldn't call this yourself
    * most of the more user-friendly functions call this
    */
  function gogo_gathercontent($function, $params) {

    if ($this->debug) {
      var_dump($params);
    }

    $httpheader = array('Accept: application/vnd.gathercontent.v0.5+json');
    $up         = $this->cfg['username'] . ':' . $this->cfg['apikey'];

    $url  = 'https://api.gathercontent.com/';
    $url .= $function;
    if ($params) {
      $ps = array();
      foreach ($params as $pk => $pv) {
        $ps[] = $pk . '=' . $pv;
      }
      $url .= '?' . implode('&', $ps);
    }

    if ($this->debug) {
      var_dump($url);
    }

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $httpheader);
    curl_setopt( $ch, CURLOPT_USERPWD, $up);
    curl_setopt( $ch, CURLOPT_URL, $url);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
    $response = json_decode( curl_exec( $ch ), TRUE );
    curl_close( $ch );

    if (!$response) {
      echo 'ERROR: No response from api.gathercontent.com' . chr(10);
      exit;
    } else if (array_key_exists('error', $response) && $response['error']) {
      echo 'ERROR: ' . $response['error'] . chr(10);
      return false;
    }

    return $response;

  }  
  
}


