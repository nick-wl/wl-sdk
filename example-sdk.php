<?php

namespace WlSdkExample;

use WellnessLiving\Core\Passport\Login\Enter\EnterModel;
use WellnessLiving\Core\Passport\Login\Enter\NotepadModel;
use WellnessLiving\Wl\Report\DataModel;
use WellnessLiving\Wl\Report\WlReportGroupSid;
use WellnessLiving\Wl\Report\WlReportSid;
use WellnessLiving\WlAssertException;
use WellnessLiving\WlUserException;

require_once __DIR__.'/WellnessLiving/wl-autoloader.php';
require_once __DIR__.'/example-config.php';

try
{
  $o_config=new ExampleConfig();

  // Retrieve notepad (it is a separate step of user sign in process)
  $o_notepad=new NotepadModel($o_config);
  $o_notepad->s_login='user@example.com';
  $o_notepad->get();

  // Sign in a user
  $o_enter=new EnterModel($o_config);
  $o_enter->cookieSet($o_notepad->cookieGet()); // Keep cookies to keep session.
  $o_enter->s_login='user@example.com';
  $o_enter->s_notepad=$o_notepad->s_notepad;
  $o_enter->s_password=$o_notepad->hash('password');
  $o_enter->post();

  $o_report=new DataModel($o_config);
  $o_report->cookieSet($o_notepad->cookieGet()); // Keep cookies to keep session.
  $o_report->id_report_group=WlReportGroupSid::DAY;
  $o_report->id_report=WlReportSid::PURCHASE_ITEM_ACCRUAL_CASH;
  $o_report->k_business='2149'; // Put your business key here
  $o_report->filterSet([
    'dt_date' => '2018-08-21'
  ]);
  $o_report->get();

  $i=0;
  foreach($o_report->a_data['a_row'] as $a_row)
  {
    $i++;
    echo $i.'. '.$a_row['dt_date'].' '.$a_row['f_total']['m_amount'].' '.$a_row['o_user']['text_name'].' '.$a_row['s_item']."\r\n";
  }
}
catch(WlAssertException $e)
{
  echo $e;
  return;
}
catch(WlUserException $e)
{
  echo $e->getMessage()."\n";
  return;
}

?>