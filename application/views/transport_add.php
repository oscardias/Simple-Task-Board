<div id="container">
    
    <h2 class="blue-gradient"><?php echo (isset($id))?"Edit Transport #".$id:"New Transport"; ?></h2>
    
    <?php echo form_open('transport/save'); ?>

    <table>
        <tr>
            <td>
                <?php echo form_label('Tasks', 'tasks'); ?>
            </td>
            <td>
                <?php
                $data = array('name'        => 'tasks',
                              'id'          => 'tasks',
                              'value'       => $tasks,
                              'rows'        => '3',
                              'cols'        => '80');

                echo form_textarea($data); ?>
            </td>
        </tr>
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
        <tr>
            <td colspan="2">
                <?php echo form_submit('save', 'Save', 'class="btn-blue"'); ?>
                <?php echo form_button('cancel', 'Cancel', 'class="btn-blue" onClick="history.go(-1)"');; ?>
            </td>
        </tr>
    </table>

    <?php if (isset($id)) echo form_hidden('id', $id); ?>
    <?php echo form_close(); ?>

</div>
