<?php

include 'gcphp.php';
$hiccup = new GCPHP();

#$items = test_getItems($hiccup);

/*
$templates = test_getTemplates($hiccup);
$first_template_name = current($templates);
$template_id = $hiccup->getTemplateID($first_template_name);
echo $first_template_name . ' has id ' . $template_id . chr(10);
echo 'GET EVERYTHING USING THIS TEMPLATE' . chr(10);
$items_template = [];
foreach ($items as $item) {
  if ($item['template_id'] == $template_id) {
    $items_template[] = $item;
  }
}
echo count($items_template) . ' items use this template.' . chr(10);
*/

#test_getWorkflowStates($hiccup);

#test_getItem($hiccup, '123');

/*
$first_item = current($items);
$item_id = $first_item['id'];
#test_getItem($hiccup, $item_id);

$item_ids = [];
foreach ($items as $item) {
  $item_ids[] = $item['id'];
}
shuffle($item_ids);
$item_id = current($item_ids);
#test_getItem($hiccup, $item_id);
*/

#test_getFields($hiccup, '7860398');
#test_getFiles($hiccup, '7860398');

test_contentOps($hiccup);




function test_getItems($hiccup) {

  echo 'LOAD ALL ITEMS IN THIS PROJECT' . chr(10);
  $items = $hiccup->getItems();
  if (count($items) == 0) {
    echo 'No items?' . chr(10);
    exit;
  } else {
    echo count($items) . ' items.' . chr(10);
  }
  
  return $items;
  
}

function test_getTemplates($hiccup) {

  // load all templates
  echo 'TEMPLATES' . chr(10);
  $templates = $hiccup->getTemplates();
  var_dump($templates);
  return $templates;

}

function test_getWorkflowStates($hiccup) {

  // load all workflow states
  echo 'WORKFLOW STATES' . chr(10);
  $states = $hiccup->getWorkflowStates();
  var_dump($states);

}

function test_getItem($hiccup, $item_id) {

  echo 'LOAD AN ITEM' . chr(10);
  $item = $hiccup->getItem($item_id);
  if ($item) {
    $n = $hiccup->getItemName($item);
    $t = $hiccup->getItemTemplate($item);
    $s = $hiccup->getItemStatus($item);
    
    echo $n . chr(10);
    echo '  is using template "' . $t . '"' . chr(10);
    echo '  has status of "' . $s . '"' . chr(10);
    echo chr(10);
  }

}

function test_getFields($hiccup, $item_id) {
  
  $fields = [
    'Reunion (optional)',
    'Quote',
    'Nope'
  ];
  $values = [];
  
  // super specific test cases
  // but you should be able to change the names of the fields as necessary
  $item = $hiccup->getItem($item_id);
  if ($item) {
    
    $n = $hiccup->getItemName($item);
    foreach ($fields as $f) {
      $values[$f] = $hiccup->getItemValue($item, $f);
    }
    
    echo $n . chr(10);
    foreach ($values as $f => $v) {
      if ($v === FALSE) {
        echo '  ' . $f . ' does not exist.' . chr(10);
      } else {
        echo '  ' . $f . ' = ' . $v . chr(10);
      }
    }
    
  }
  
}


function test_getFiles($hiccup, $item_id) {
  
  $fields = [
    'Vertical Image',
    'Thumbnail image',
    'Horizontal Image',
    'Nope'
  ];
  $values = [];
  
  // super specific test cases
  // but you should be able to change the names of the fields as necessary
  $item = $hiccup->getItem($item_id);
  $files = $hiccup->getItemFiles($item_id);
  
  if ($item && $files) {
    
    $n = $hiccup->getItemName($item);
    foreach ($fields as $f) {
      $values[$f] = $hiccup->getItemFile($item, $f, $files);
    }
    
    echo $n . chr(10);
    foreach ($values as $f => $v) {
      if ($v === FALSE) {
        echo '  ' . $f . ' does not exist.' . chr(10);
      } else {
        echo '  ' . $f . ' = ' . $v . chr(10);
      }
    }
    
  }  
  
}


function test_contentOps($hiccup) {
  
  $content = [
    '<p>Lorem ipsum<br> dolor sit amet.</p>',
    '<p><br><br> </p>',
    '<p>   </p>',
    '<p></p>',
  ];
  
  foreach ($content as $c) {
    echo '###' . chr(10);
    echo $c . chr(10);
    echo $hiccup->cleanContent($c) . chr(10);
  }
  
}
