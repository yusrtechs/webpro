<?php defined('BASEPATH') or exit('No direct script access allowed'); 

?>
<style>
.previewImg{
width: 76px;
    margin: 0 auto;
    display: inline-block;
    margin: 0 26px;
    border: 1px solid #ccc;
}

.col-md-12 > .row {
    margin: 3px 0;
    padding: 5px 0;
    background: #eaeaea;
}
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cogs"></i><?= lang('slider_settings'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
            <!-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#uploadModal">Upload file</button> -->

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = ['data-toggle' => 'validator', 'role' => 'form'];
                echo admin_form_open_multipart('shop_settings/slider', $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 text-center ">
                            <?php
                            if(property_exists($slider_settings[0],'image')){
                                ?>
                               <img class="previewImg triggerBTN" data-toggle="modal" name="images[imageId1]" data-target="#uploadModal" src="<?=base_url('/assets/uploads/'.$slider_settings[0]->image)?>">
                                <?php
                            }else{
                                ?>
                                                            <button type="button" name="images[imageId1]" class="btn btn-info triggerBTN" data-toggle="modal" data-target="#uploadModal"> <?= lang('image', 'image1'); ?> 2</button>

                                <?php
                            }
                            ?>
                            <span class="imgContainer"></span>

                            <div style="display:none" class="d-none form-group">
                                    <?= lang('image', 'image1'); ?> 
                                    <input id="image1" type="file" data-browse-label="<?= lang('browse'); ?>" name="image1" data-show-upload="false" data-show-preview="false"  class="form-control d-none file">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('link', 'link1'); ?> 1
                                    <?= form_input('link1', set_value('link1', (isset($slider_settings[0]->link) ? $slider_settings[0]->link : '')), 'class="form-control tip" id="link1"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('caption', 'caption1'); ?> 1
                                    <?= form_input('caption1', set_value('caption1', (isset($slider_settings[0]->caption) ? $slider_settings[0]->caption : '')), 'class="form-control tip" id="caption1"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('caption', 'caption1'); ?> 1 EN
                                    <?= form_input('caption_en1', set_value('caption1', (isset($slider_settings[0]->caption_en) ? $slider_settings[0]->caption_en : '')), 'class="form-control tip" id="caption_en2"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-3 text-center ">
                             <?php
                            if(property_exists($slider_settings[1],'image')){
                                ?>
                               <img class="previewImg triggerBTN" name="images[imageId2]" data-toggle="modal" data-target="#uploadModal" src="<?=base_url('/assets/uploads/'.$slider_settings[1]->image)?>">
                                <?php
                            }else{
                                ?>
                                                            <button type="button" name="images[imageId2]" class="triggerBTN btn btn-info" data-toggle="modal" data-target="#uploadModal"> <?= lang('image', 'image1'); ?> 2</button>

                                <?php
                            }
                            ?>
                                                        <span class="imgContainer"></span>

                                <div style="display:none" class="d-none form-group">
                                    <?= lang('image', 'image2'); ?> 2
                                    <input id="image2" type="file" data-browse-label="<?= lang('browse'); ?>" name="image2" data-show-upload="false" data-show-preview="false" class="form-control file">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('link', 'link2'); ?> 2 
                                    <?= form_input('link2', set_value('link2', (isset($slider_settings[1]->link) ? $slider_settings[1]->link : '')), 'class="form-control tip" id="link2"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?= lang('caption', 'caption2'); ?> 2
                                    <?= form_input('caption2', set_value('caption2', (isset($slider_settings[1]->caption) ? $slider_settings[1]->caption : '')), 'class="form-control tip" id="caption2"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('caption', 'caption2'); ?> 2 EN
                                    <?= form_input('caption_en2', set_value('caption2', (isset($slider_settings[1]->caption_en) ? $slider_settings[1]->caption_en : '')), 'class="form-control tip" id="caption_en2"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-3 text-center ">
                             <?php
                            if(property_exists($slider_settings[2],'image')){
                                ?>
                               <img class="previewImg triggerBTN" data-toggle="modal" name="images[imageId3]" data-target="#uploadModal" src="<?=base_url('/assets/uploads/'.$slider_settings[2]->image)?>">
                                <?php
                            }else{
                                ?>
                                                            <button type="button" name="images[imageId3]" class="triggerBTN btn btn-info" data-toggle="modal" data-target="#uploadModal"> <?= lang('image', 'image1'); ?> 3</button>

                                <?php
                            }
                            ?>
                                                                                    <span class="imgContainer"></span>

                                <div style="display:none" class="d-none form-group">
                                    <?= lang('image', 'image3'); ?> 3
                                    <input id="image3" type="file"  data-browse-label="<?= lang('browse'); ?>" name="image3" data-show-upload="false" data-show-preview="false" class="form-control file">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('link', 'link3'); ?> 3
                                    <?= form_input('link3', set_value('link3', (isset($slider_settings[2]->link) ? $slider_settings[2]->link : '')), 'class="form-control tip" id="link3"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                    <?= lang('caption', 'caption3'); ?> 3
                                    <?= form_input('caption3', set_value('caption3', (isset($slider_settings[2]->caption) ? $slider_settings[2]->caption : '')), 'class="form-control tip" id="caption3"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('caption', 'caption3'); ?> EN 3
                                    <?= form_input('caption_en3', set_value('caption3', (isset($slider_settings[2]->caption_en) ? $slider_settings[2]->caption_en : '')), 'class="form-control tip" id="caption_en3"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-3 text-center ">
                             <?php
                            if(property_exists($slider_settings[3],'image')){
                                ?>
                               <img class="previewImg triggerBTN" name="images[imageId4]" data-toggle="modal" data-target="#uploadModal" src="<?=base_url('/assets/uploads/'.$slider_settings[3]->image)?>">
                                <?php
                            }else{
                                ?>
                                                            <button type="button" name="images[imageId4]" class="triggerBTN btn btn-info" data-toggle="modal" data-target="#uploadModal"> <?= lang('image', 'image1'); ?> 4</button>

                                <?php
                            }
                            ?>
                                                                                                                
                                <span class="imgContainer"></span>

                                <div style="display:none" class="d-none form-group">
                                    <?= lang('image', 'image4'); ?> 4
                                    <input id="image4" type="file" data-browse-label="<?= lang('browse'); ?>" name="image4" data-show-upload="false" data-show-preview="false" class="form-control file">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('link', 'link4'); ?> 4
                                    <?= form_input('link4', set_value('link4', (isset($slider_settings[3]->link) ? $slider_settings[3]->link : '')), 'class="form-control tip" id="link4"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                    <?= lang('caption', 'caption4'); ?> 4
                                    <?= form_input('caption4', set_value('caption4', (isset($slider_settings[3]->caption) ? $slider_settings[3]->caption : '')), 'class="form-control tip" id="caption4"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('caption', 'caption4'); ?> EN 4
                                    <?= form_input('caption_en4', set_value('caption4', (isset($slider_settings[3]->caption_en) ? $slider_settings[3]->caption_en : '')), 'class="form-control tip" id="caption_en4"'); ?>
                                </div>
                         
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-3 text-center ">
                             <?php
                            if(property_exists($slider_settings[4],'image')){
                                ?>
                               <img class="previewImg triggerBTN" name="images[imageId5]" data-toggle="modal" data-target="#uploadModal" src="<?=base_url('/assets/uploads/'.$slider_settings[4]->image)?>">
                                <?php
                            }else{
                                ?>
                                <button type="button" name="images[imageId5]" class="triggerBTN btn btn-info" data-toggle="modal" data-target="#uploadModal"> <?= lang('image', 'image1'); ?> 5</button>

                                <?php
                            }
                            ?>
                     <span class="imgContainer"></span>

                                <div style="display:none" class="d-none form-group">
                                    <?= lang('image', 'image5'); ?> 5
                                    <input id="image5" type="file" data-browse-label="<?= lang('browse'); ?>" name="image5" data-show-upload="false" data-show-preview="false" class="form-control file">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?= lang('link', 'link5'); ?> 5
                                    <?= form_input('link5', set_value('link5', (isset($slider_settings[4]->link) ? $slider_settings[4]->link : '')), 'class="form-control tip" id="link5"'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="form-group">
                                    <?= lang('caption', 'caption5'); ?> 5
                                    <?= form_input('caption5', set_value('caption5', (isset($slider_settings[4]->caption) ? $slider_settings[4]->caption : '')), 'class="form-control tip" id="caption5"'); ?>
                                </div>
                                <div class="form-group">
                                    <?= lang('caption', 'caption5'); ?> 5 EN
                                    <?= form_input('caption_en5', set_value('caption5', (isset($slider_settings[4]->caption_en) ? $slider_settings[4]->caption_en : '')), 'class="form-control tip" id="caption_en5"'); ?>
                                </div>
                            </div>
                        </div>

                        <?= form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
