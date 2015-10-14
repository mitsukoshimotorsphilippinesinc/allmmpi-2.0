<table class='table table-striped table-bordered'>
    <thead>
    <tr>
        <th>Member</th>
        <th>Account ID</th>
        <th>Sponsor ID</th>
        <th>Date Registered</th>
    </tr>
    </thead>
    <tbody>
    <?php if(empty($member_accounts)): ?>
        <tr><td colspan='6' style='text-align:center;'><strong>No Records Found</strong></td></tr>
    <?php else: ?>
        <?php foreach ($member_accounts as $member_account):
            $account_name = $member_details[$member_account->member_id]->name;
    ?>
        <tr>
            <td><?= $account_name; ?></td>
            <td><?= $member_account->account_id; ?></td>
            <td><?= $member_account->sponsor_id; ?></td>
            <td><?= $member_account->insert_timestamp; ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
