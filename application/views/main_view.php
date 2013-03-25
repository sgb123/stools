<?php
/**
 * @view Main view
 */
$class = '';
$msg = '';
if(!empty($message))
{
    $class = $message['type'];
    $msg = $message['message'];
}
?>
<div class="row hero-unit">
    <div class="<?=$class;?>-info"><?=$msg;?></div>
    <?php if($logged_in):?>
        <div class="span4">
            <h5>Logged in as: <span id="rootInfo"><?=$root_account->login;?></span></h5>
        </div>
        <div class="clr"></div>
        <div class="row">
            <div class="span10">
                <h5>Account List</h5>
                <table id="accounts-table" class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Login</th>
                        <th>Name</th>
                        <th>Campaigns</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($accounts as $account):?>
                        <tr>
                            <td><code><?=$account->customerId;?></code></td>
                            <td><?=$account->login;?></td>
                            <td><?=$account->name;?></td>
                            <td><i class="icon-eye-open" data-customer-id="<?=$account->customerId;?>"></i></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <div class="span6">
                <h5>Account active Campaigns</h5>
                <table id="account-campaigns-table" class="table table-bordered table-condensed table-hover account-campaigns-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    <?php endif;?>
</div>