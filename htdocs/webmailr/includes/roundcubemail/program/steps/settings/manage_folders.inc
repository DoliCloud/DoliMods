<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/settings/manage_folders.inc                             |
 |                                                                       |
 | This file is part of the RoundCube Webmail client                     |
 | Copyright (C) 2005-2009, RoundCube Dev. - Switzerland                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Provide functionality to create/delete/rename folders               |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: manage_folders.inc,v 1.1 2011/08/01 19:22:06 eldy Exp $

*/

// WARNING: folder names in UI are encoded with RCMAIL_CHARSET

// init IMAP connection
$RCMAIL->imap_connect();

// subscribe to one or more mailboxes
if ($RCMAIL->action=='subscribe')
  {
  if ($mbox = get_input_value('_mbox', RCUBE_INPUT_POST, false, 'UTF7-IMAP'))
    $IMAP->subscribe(array($mbox));
  }

// unsubscribe one or more mailboxes
else if ($RCMAIL->action=='unsubscribe')
  {
  if ($mbox = get_input_value('_mbox', RCUBE_INPUT_POST, false, 'UTF7-IMAP'))
    $IMAP->unsubscribe(array($mbox));
  }

// enable threading for one or more mailboxes
else if ($RCMAIL->action=='enable-threading')
  {
  if ($mbox = get_input_value('_mbox', RCUBE_INPUT_POST, false, 'UTF7-IMAP'))
    rcube_set_threading($mbox, true);
  }

// enable threading for one or more mailboxes
else if ($RCMAIL->action=='disable-threading')
  {
  if ($mbox = get_input_value('_mbox', RCUBE_INPUT_POST, false, 'UTF7-IMAP'))
    rcube_set_threading($mbox, false);
  }

// create a new mailbox
else if ($RCMAIL->action=='create-folder')
  {
  if (!empty($_POST['_name']))
    {
    $name = trim(get_input_value('_name', RCUBE_INPUT_POST, FALSE, 'UTF7-IMAP'));
    $create = $IMAP->create_mailbox($name, TRUE);
    }
  
  if ($create && $OUTPUT->ajax_call)
    {
    $delimiter = $IMAP->get_hierarchy_delimiter();
    $folderlist = $IMAP->list_unsubscribed();
    $index = array_search($create, $folderlist);
    $before = $index !== false && isset($folderlist[$index+1]) ? rcube_charset_convert($folderlist[$index+1], 'UTF7-IMAP') : false;
    
    $create = rcube_charset_convert($create, 'UTF7-IMAP');
    $foldersplit = explode($delimiter, $create);
    $display_create = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', substr_count($create, $delimiter)) . $foldersplit[count($foldersplit)-1];

    $OUTPUT->command('add_folder_row', $create, $display_create, false, $before);
    }
  else if (!$create)
    {
    $OUTPUT->show_message('errorsaving', 'error');
    }
  }

// rename a mailbox
else if ($RCMAIL->action=='rename-folder')
  {
  if (!empty($_POST['_folder_oldname']) && !empty($_POST['_folder_newname']))
    {
    $name_utf8 = trim(get_input_value('_folder_newname', RCUBE_INPUT_POST));
    $oldname_utf8 = get_input_value('_folder_oldname', RCUBE_INPUT_POST);
    $name = rcube_charset_convert($name_utf8, RCMAIL_CHARSET, 'UTF7-IMAP');
    $oldname = rcube_charset_convert($oldname_utf8, RCMAIL_CHARSET, 'UTF7-IMAP');

    $rename = $IMAP->rename_mailbox($oldname, $name);
    }

  // update per-folder options for modified folder and its subfolders
  if ($rename) {
    $a_threaded = $RCMAIL->config->get('message_threading', array()); 
    $delimiter = $IMAP->get_hierarchy_delimiter();
    $oldprefix = '/^' . preg_quote($oldname . $delimiter, '/') . '/';
    foreach ($a_threaded as $key => $val)
      if ($key == $oldname) {
        unset($a_threaded[$key]);
	$a_threaded[$name] = true;
        }
      else if (preg_match($oldprefix, $key)) {
        unset($a_threaded[$key]);
	$a_threaded[preg_replace($oldprefix, $name.$delimiter, $key)] = true;      
      }
      
    $RCMAIL->user->save_prefs(array('message_threading' => $a_threaded));
    }

  if ($rename && $OUTPUT->ajax_call)
    {
    $folderlist = $IMAP->list_unsubscribed();
    $delimiter = $IMAP->get_hierarchy_delimiter();

    $regexp = '/^' . preg_quote($rename . $delimiter, '/') . '/';

    // subfolders
    for ($x=sizeof($folderlist)-1; $x>=0; $x--)
      {
      if (preg_match($regexp, $folderlist[$x]))
        {
        $oldfolder = $oldname . $delimiter . preg_replace($regexp, '', $folderlist[$x]);
        $foldersplit = explode($delimiter, $folderlist[$x]);
        $level = count($foldersplit) - 1;
        $display_rename = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) 
          . rcube_charset_convert($foldersplit[$level], 'UTF7-IMAP');

        $before = isset($folderlist[$x+1]) ? rcube_charset_convert($folderlist[$x+1], 'UTF7-IMAP') : false;
        
        $OUTPUT->command('replace_folder_row', rcube_charset_convert($oldfolder, 'UTF7-IMAP'),
          rcube_charset_convert($folderlist[$x], 'UTF7-IMAP'), $display_rename, $before);
        }
      }

    $foldersplit = explode($delimiter, $rename);
    $level = count($foldersplit) - 1;
    $display_rename = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . rcube_charset_convert($foldersplit[$level], 'UTF7-IMAP');
    $index = array_search($rename, $folderlist);
    $before = $index !== false && isset($folderlist[$index+1]) ? rcube_charset_convert($folderlist[$index+1], 'UTF7-IMAP') : false;

    $OUTPUT->command('replace_folder_row', $oldname_utf8, rcube_charset_convert($rename, 'UTF7-IMAP'), $display_rename, $before);
    $OUTPUT->command('reset_folder_rename');
    }
  else if (!$rename && $OUTPUT->ajax_call)
    {
    $OUTPUT->command('reset_folder_rename');
    $OUTPUT->show_message('errorsaving', 'error');
    }
  else if (!$rename)
    $OUTPUT->show_message('errorsaving', 'error');
  }

// delete an existing IMAP mailbox
else if ($RCMAIL->action=='delete-folder')
  {
  $a_mboxes = $IMAP->list_unsubscribed();
  $delimiter = $IMAP->get_hierarchy_delimiter();
  
  $mboxes_utf8 = get_input_value('_mboxes', RCUBE_INPUT_POST);
  $mboxes = rcube_charset_convert($mboxes_utf8, RCMAIL_CHARSET, 'UTF7-IMAP');

  if ($mboxes)
    $deleted = $IMAP->delete_mailbox(array($mboxes));

  if ($OUTPUT->ajax_call && $deleted)
    {
    $OUTPUT->command('remove_folder_row', $mboxes_utf8);
    foreach ($a_mboxes as $mbox)
      {
      if (preg_match('/^'. preg_quote($mboxes.$delimiter, '/') .'/', $mbox))
        {
        $OUTPUT->command('remove_folder_row', rcube_charset_convert($mbox, 'UTF7-IMAP'));
        }
      }
    $OUTPUT->show_message('folderdeleted', 'confirmation');
    }
  else if (!$deleted)
    {
    $OUTPUT->show_message('errorsaving', 'error');
    }
  }

if ($OUTPUT->ajax_call)
  $OUTPUT->send();


// build table with all folders listed by server
function rcube_subscription_form($attrib)
  {
  global $RCMAIL, $IMAP, $CONFIG, $OUTPUT;

  $threading_supported = $IMAP->get_capability('thread=references')
    || $IMAP->get_capability('thread=orderedsubject')
    || $IMAP->get_capability('thread=refs');

  list($form_start, $form_end) = get_form_tags($attrib, 'folders');
  unset($attrib['form']);
  
  if (!$attrib['id'])
    $attrib['id'] = 'rcmSubscriptionlist';

  $table = new html_table();

  // add table header
  $table->add_header('name', rcube_label('foldername'));
  $table->add_header('msgcount', rcube_label('messagecount'));
  $table->add_header('subscribed', rcube_label('subscribed'));
  if ($threading_supported)
    $table->add_header('threaded', rcube_label('threaded'));
  $table->add_header('rename', '&nbsp;');
  $table->add_header('delete', '&nbsp;');

  // get folders from server
  $IMAP->clear_cache('mailboxes');

  $a_unsubscribed = $IMAP->list_unsubscribed();
  $a_subscribed = $IMAP->list_mailboxes();
  $a_threaded = $a_threaded_copy = $RCMAIL->config->get('message_threading', array()); 
  $delimiter = $IMAP->get_hierarchy_delimiter();
  $a_js_folders = $seen_folders = $list_folders = array();

  // pre-process folders list
  foreach ($a_unsubscribed as $i => $folder) {
    $foldersplit = explode($delimiter, $folder);
    $name = rcube_charset_convert(array_pop($foldersplit), 'UTF7-IMAP');
    $parent_folder = join($delimiter, $foldersplit);
    $level = count($foldersplit);

    // add any necessary "virtual" parent folders
    if ($parent_folder && !$seen[$parent_folder]) {
      for ($i=1; $i<=$level; $i++) {
	$ancestor_folder = join($delimiter, array_slice($foldersplit, 0, $i));
	if ($ancestor_folder && !$seen[$ancestor_folder]++) {
	  $ancestor_name = rcube_charset_convert($foldersplit[$i-1], 'UTF7-IMAP');
	  $list_folders[] = array('id' => $ancestor_folder, 'name' => $ancestor_name, 'level' => $i-1, 'virtual' => true);
	}
      }
    }
    
    unset($a_threaded_copy[$folder]);
    
    $list_folders[] = array('id' => $folder, 'name' => $name, 'level' => $level);
    $seen[$folder]++;
  }

  // remove 'message_threading' option for not existing folders
  if ($a_threaded_copy) {
    foreach ($a_threaded_copy as $key => $val)
      unset($a_threaded[$key]);
    unset($a_threaded_copy);
    $RCMAIL->user->save_prefs(array('message_threading' => $a_threaded));
  }

  $checkbox_subscribe = new html_checkbox(array(
    'name' => '_subscribed[]',
    'onclick' => JS_OBJECT_NAME.".command(this.checked?'subscribe':'unsubscribe',this.value)",
  ));
  $checkbox_threaded = new html_checkbox(array(
    'name' => '_threaded[]',
    'onclick' => JS_OBJECT_NAME.".command(this.checked?'enable-threading':'disable-threading',this.value)",
  ));
  
  if (!empty($attrib['deleteicon']))
    $del_button = html::img(array('src' => $CONFIG['skin_path'] . $attrib['deleteicon'], 'alt' => rcube_label('delete')));
  else
    $del_button = rcube_label('delete');

  if (!empty($attrib['renameicon']))
    $edit_button = html::img(array('src' => $CONFIG['skin_path'] . $attrib['renameicon'], 'alt' => rcube_label('rename')));
  else
    $edit_button = rcube_label('rename');
    
  // create list of available folders
  foreach ($list_folders as $i => $folder) {
    $idx = $i + 1;
    $subscribed = in_array($folder['id'], $a_subscribed);
    $threaded = $a_threaded[$folder['id']];
    $protected = ($CONFIG['protect_default_folders'] == true && in_array($folder['id'], $CONFIG['default_imap_folders']));
    $classes = array($i%2 ? 'even' : 'odd');
    $folder_js = JQ($folder['id']);
    $display_folder = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $folder['level']) . ($protected ? rcmail_localize_foldername($folder['id']) : $folder['name']);
    $folder_utf8 = rcube_charset_convert($folder['id'], 'UTF7-IMAP');
    
    if ($folder['virtual'])
      $classes[] = 'virtual';
    
    $table->add_row(array('id' => 'rcmrow'.$idx, 'class' => join(' ', $classes)));
    
    $table->add('name', Q($display_folder));
    $table->add('msgcount', ($folder['virtual'] ? '' : $IMAP->messagecount($folder['id'], 'ALL', false, false)));
    $table->add('subscribed', ($protected || $folder['virtual']) ? ($subscribed ? '&nbsp;&#x2022;' : '&nbsp;') :
        $checkbox_subscribe->show(($subscribed ? $folder_utf8 : ''), array('value' => $folder_utf8)));
    if ($threading_supported) {
      $table->add('threaded', $folder['virtual'] ? '' :
            $checkbox_threaded->show(($threaded ? $folder_utf8 : ''), array('value' => $folder_utf8)));
    }
    
    // add rename and delete buttons
    if (!$protected && !$folder['virtual']) {
      $table->add('rename', html::a(array('href' => "#rename", 'title' => rcube_label('renamefolder')), $edit_button));
      $table->add('delete', html::a(array('href' => "#delete", 'title' => rcube_label('deletefolder')), $del_button));
    }
    else {
      $table->add('rename', '&nbsp;');
      $table->add('delete', '&nbsp;');
    }
    
    $a_js_folders['rcmrow'.$idx] = array($folder_utf8, $display_folder, $protected || $folder['virtual']);
  }

  rcmail::get_instance()->plugins->exec_hook('folders_list', array('table'=>$table));

  $OUTPUT->add_gui_object('subscriptionlist', $attrib['id']);
  $OUTPUT->set_env('subscriptionrows', $a_js_folders);
  $OUTPUT->set_env('defaultfolders', $CONFIG['default_imap_folders']);
  $OUTPUT->set_env('delimiter', $delimiter);

  return $form_start . $table->show($attrib) . $form_end;
  }


function rcube_create_folder_form($attrib)
  {
  global $OUTPUT;
  
  list($form_start, $form_end) = get_form_tags($attrib, 'create-folder');
  unset($attrib['form']);

  if ($attrib['hintbox'])
    $OUTPUT->add_gui_object('createfolderhint', $attrib['hintbox']);

  // return the complete edit form as table
  $out = "$form_start\n";

  $input = new html_inputfield(array('name' => '_folder_name'));
  $out .= $input->show();
  
  if (get_boolean($attrib['button']))
    {
    $button = new html_inputfield(array('type' => 'button',
                                    'value' => rcube_label('create'),
                                    'onclick' => JS_OBJECT_NAME.".command('create-folder',this.form)"));
    $out .= $button->show();
    }

  $out .= "\n$form_end";

  return $out;
  }

function rcube_rename_folder_form($attrib)
  {
  global $CONFIG, $IMAP;

  list($form_start, $form_end) = get_form_tags($attrib, 'rename-folder');
  unset($attrib['form']);

  // return the complete edit form as table
  $out = "$form_start\n";

  $a_unsubscribed = $IMAP->list_unsubscribed();
  $select_folder = new html_select(array('name' => '_folder_oldname', 'id' => 'rcmfd_oldfolder'));

  foreach ($a_unsubscribed as $i => $folder)
    {
    if ($CONFIG['protect_default_folders'] == TRUE && in_array($folder,$CONFIG['default_imap_folders'])) 
      continue;

    $select_folder->add($folder);
    }

  $out .= $select_folder->show();

  $out .= " to ";
  $inputtwo = new html_inputfield(array('name' => '_folder_newname'));
  $out .= $inputtwo->show();

  if (get_boolean($attrib['button']))
    {
    $button = new html_inputfield(array('type' => 'button',
                                    'value' => rcube_label('rename'),
                                    'onclick' => JS_OBJECT_NAME.".command('rename-folder',this.form)"));
    $out .= $button->show();
    }

  $out .= "\n$form_end";
  
  return $out;
  }


// (un)set 'threading' for selected folder
function rcube_set_threading($mbox, $state=true)
  {
  global $RCMAIL;
  $mbox = (array)$mbox;
  $a_prefs = (array)$RCMAIL->config->get('message_threading');

  if ($state) {
    foreach ($mbox as $box)
      $a_prefs[$box] = true;
    }
  else {
    foreach ($mbox as $box)
      unset($a_prefs[$box]);
    }

  $RCMAIL->user->save_prefs(array('message_threading' => $a_prefs));
  }


$OUTPUT->set_pagetitle(rcube_label('folders'));
$OUTPUT->include_script('list.js');

// register UI objects
$OUTPUT->add_handlers(array(
  'foldersubscription' => 'rcube_subscription_form',
  'createfolder' => 'rcube_create_folder_form',
  'renamefolder' => 'rcube_rename_folder_form'
));

// add some labels to client
$OUTPUT->add_label('deletefolderconfirm','addsubfolderhint','forbiddencharacter','folderdeleting','folderrenaming','foldercreating','foldermoving');

$OUTPUT->send('managefolders');

