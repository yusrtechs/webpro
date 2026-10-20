<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-building-o"></i> Agents Management
            <small>Add, Edit</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Enter agent information</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php $this->load->helper("form"); ?>
                    <form role="form" id="addUser" action="<?= base_url() ?>agents/update/process" method="post" role="form">
                        <input type="hidden" value="<?= $agent_info->id; ?>" name="agent_id" id="agent_id" />
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control required" value="<?= $agent_info->name; ?>" id="name" name="name" maxlength="128">
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email address</label>
                                        <input type="text" class="form-control required email" id="email" value="<?= $agent_info->email; ?>" name="email" maxlength="128">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country" class="form-label">Country</label>
                                        <select id="country" name="country_code" class="form-control required">
                                            <option>Select country</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state" class="form-label">City</label>
                                        <select id="state" name="city_code" class="form-control required">
                                            <option>-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="phone" class="form-control required digits" id="phone" name="phone" value="<?= $agent_info->phone; ?>" maxlength="20">
                                    </div>
                                </div>
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="logo">Logo</label>
                                        <input type="file" class="form-control required" id="logo" name="logo">
                                    </div>
                                </div> -->
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" class="form-control required" id="address"><?= $agent_info->address; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer text-center">
                            <input type="submit" class="btn btn-primary" value="Save" />
                            <!-- <input type="reset" class="btn btn-default" value="Reset" /> -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <?= validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?= base_url(); ?>assets/js/addUser.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>assets/js/country-states.js" type="text/javascript"></script>
<script>
    const user_city_code = '<?= $agent_info->city_code ?>'
    // user country code for selected option
    var user_country_code = '<?= $agent_info->country_code ?>';

    (() => {
        // script https://www.html-code-generator.com/html/drop-down/state-name

        // Get the country name and state name from the imported script.
        const country_array = country_and_states.country;
        const states_array = country_and_states.states;

        const id_state_option = document.getElementById("state");
        const id_country_option = document.getElementById("country");

        const createCountryNamesDropdown = () => {
            let option = '';
            option += '<option value="">select country</option>';

            for (let country_code in country_array) {
                // set selected option user country
                let selected = (country_code == user_country_code) ? ' selected' : '';
                option += '<option value="' + country_code + '"' + selected + '>' + country_array[country_code] + '</option>';
            }
            id_country_option.innerHTML = option;
        };

        const createStatesNamesDropdown = () => {
            let selected_country_code = id_country_option.value;
            // get state names
            let state_names = states_array[selected_country_code];

            // if invalid country code
            if (!state_names) {
                id_state_option.innerHTML = '<option>select state</option>';
                return;
            }
            let option = '';
            option += '<select id="state">';
            option += '<option>select state</option>';
            for (let i = 0; i < state_names.length; i++) {
                selected = '';
                if (user_city_code == state_names[i].code)
                    selected = 'selected="selected"';

                option += '<option value="' + state_names[i].code + '" ' + selected + '>' + state_names[i].name + ' </option>';
            }
            option += '</select>';
            id_state_option.innerHTML = option;
        };

        // country select change event
        id_country_option.addEventListener('change', createStatesNamesDropdown);

        createCountryNamesDropdown();
        createStatesNamesDropdown();
    })();
</script>