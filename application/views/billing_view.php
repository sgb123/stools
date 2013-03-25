<?php
/**
 * @view Billing view
 */
?>
<div class="row hero-unit">
    <div class="row">
        <div class="span10">
            <div class="form-inline account-form">
                <label class="label">Account:</label>
                <select id="accounts-select">
                <?php foreach($accounts as $account):?>
                    <option value="<?=$account->customerId;?>"><?=$account->login;?></option>
                <?php endforeach;?>
                </select>
                <label class="label">Date:</label>
                <input type="text" id="date-input" class="span2"/>
                <button type="button" id="get-btn" class="btn">Get</button>
            </div>
        </div>
    </div>
    <div class="row alerts-container">
        <div class="span12">
            <h5>Customer Accounts Alerts</h5>
            <?php if(empty($alerts)):?>
            <div class="span8 alert alert-info"><?=$message?></div>
            <?php else:?>
            <table id="customer-alerts-table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Alert Type</th>
                        <th>Severity</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($alerts as $alert):?>
                    <tr>
                        <td><code><?=$alert['customer_id'];?></code></td>
                        <td><code><?=$alert['alert_type'];?></code></td>
                        <td><code><?=$alert['severity'];?></code></td>
                        <td><code><?=$alert['details'];?></code></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <?php endif;?>
        </div>

    </div>
</div>