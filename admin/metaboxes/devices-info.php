<table class="form-table">
    <tr>
        <th scope="row">
            <label for="device_name">Nazwa urządzenia</label>
        </th>
        <td>
            <input type="text" name="device_name" id="device_name" class="regular-text" required value="<?= $device_name ?>"/>
            <p class="description">Twoja własna, krótka nazwa urządzenia</p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="device_name">Klucz API</label>
        </th>
        <td>
            <input type="text" disabled value="<?= $api_key ?>" style="background-color: #efefef; border-color: #efefef; color: #000; cursor: text"/>
            <input type="hidden" name="device_api_key" id="device_api_key" value="<?= $api_key ?>" />
        </td>
    </tr>
</table>