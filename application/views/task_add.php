
<div id="container">
    
    <h2 class="blue-gradient"><?php echo (isset($id))?"Edit Task #".$id:"New Task"; ?></h2>

    <?php echo form_open('task/save'); ?>

    <table>
        <tr>
            <td>
                <?php echo form_label('Title', 'title'); ?>
            </td>
            <td>
                <?php echo form_input('title', $title, 'maxlength="50"'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Priority', 'priority'); ?>
            </td>
            <td>
                <?php $options = array('0' => 'Very High', '1' => 'High', '2' => 'Normal', '3' => 'Low', '4' => 'Very Low');
                      echo form_dropdown('priority', $options, $priority); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Description', 'text'); ?>
            </td>
            <td>
                <?php
                $data = array('name'        => 'description',
                              'id'          => 'description',
                              'value'       => $description,
                              'rows'        => '6',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <?php if (isset($id)) { ?>
        <tr>
            <td>
                <?php echo form_label('Files changed', 'files'); ?>
            </td>
            <td>
                <?php
                $data = array('name'        => 'files',
                              'id'          => 'files',
                              'value'       => $files,
                              'rows'        => '3',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo form_label('Database changes', 'database'); ?>
            </td>
            <td>
                <?php
                $data = array('name'        => 'database',
                              'id'          => 'database',
                              'value'       => $database,
                              'rows'        => '3',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
                <td colspan="2">
                    <?php if (isset($id)) echo form_hidden('id', $id); ?>
                    <?php if (isset($status)) echo form_hidden('status', $status); ?>

                    <?php echo form_submit('save', 'Save', 'class="btn-blue"'); ?>
                    <?php echo form_button('cancel', 'Cancel', 'class="btn-blue" onClick="history.go(-1)"');; ?>
                </td>
        </tr>
    </table>

    <?php echo form_close(); ?>

</div>