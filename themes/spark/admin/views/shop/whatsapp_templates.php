<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="spark-whatsapp-module">
    <!-- Green WhatsApp Header Banner (Matching React Reference) -->
    <div class="spark-whatsapp-banner">
        <div class="spark-whatsapp-banner-content">
            <div class="spark-whatsapp-banner-badge">
                <i class="fa fa-whatsapp"></i>
            </div>
            <div>
                <h1 class="spark-whatsapp-banner-title"><?= lang('whatsapp_service'); ?></h1>
                <p class="spark-whatsapp-banner-subtitle"><?= lang('whatsapp_service_sub'); ?></p>
            </div>
        </div>
    </div>

    <!-- 4-Tab Navigation Bar -->
    <div class="spark-whatsapp-nav">
        <a href="<?= admin_url('system_settings/whatsapp_settings'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-cogs"></i>
            <span><?= lang('Whatsapp_settings'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/send_whatsapp'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-paper-plane"></i>
            <span><?= lang('send_whatsapp'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/whatsapp_log'); ?>" class="spark-whatsapp-tab">
            <i class="fa fa-history"></i>
            <span><?= lang('Whatsapp_log'); ?></span>
        </a>
        <a href="<?= admin_url('system_settings/whatsapp_templates'); ?>" class="spark-whatsapp-tab is-active">
            <i class="fa fa-file-text-o"></i>
            <span><?= lang('Whatsapp_templates'); ?></span>
        </a>
    </div>

    <!-- Main Content Body -->
    <div class="spark-whatsapp-body">
        <div class="row">
            <!-- Templates Form Column (Matching React Screenshot 4) -->
            <div class="col-md-8 col-sm-12">
                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 800; color: var(--spark-text-primary, #0f172a); margin: 0 0 6px 0;">
                        <i class="fa fa-list-alt text-success" style="margin-inline-end: 6px;"></i>
                        <?= lang('message_templates'); ?>
                    </h3>
                    <p class="text-muted" style="font-size: 13px;"><?= lang('customize_templates_desc'); ?></p>
                </div>

                <!-- Template 1: Sales -->
                <div class="spark-whatsapp-tpl-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(34, 197, 94, 0.15); display: flex; align-items: center; justify-content: center; color: #16a34a;">
                                <i class="fa fa-shopping-cart"></i>
                            </div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--spark-text-primary, #0f172a);"><?= lang('template_sales'); ?></h4>
                        </div>
                        <span class="badge" style="background: rgba(34, 197, 94, 0.15); color: #15803d; border-radius: 6px; padding: 4px 8px;"><?= lang('sales'); ?></span>
                    </div>

                    <?= admin_form_open('system_settings/whatsapp_templates'); ?>
                    <div class="form-group" style="margin-bottom: 12px;">
                        <textarea name="whatsapp_body" class="form-control" rows="3" id="comment_sales" placeholder="<?= lang('template_body_placeholder'); ?>" style="font-size: 13.5px;"><?= isset($_POST['whatsapp_body']) ? html_entity_decode($_POST['whatsapp_body']) : html_entity_decode($sales); ?></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <input type="submit" name="submit" class="spark-btn-whatsapp-primary" value="<?= lang('save_template'); ?>" style="padding: 8px 20px; font-size: 13px;" />
                    </div>
                    <?= form_close(); ?>
                </div>

                <!-- Template 2: Quotes -->
                <div class="spark-whatsapp-tpl-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--spark-primary-subtle, rgba(99, 102, 241, 0.15)); display: flex; align-items: center; justify-content: center; color: var(--spark-primary, #6366f1);">
                                <i class="fa fa-file-text-o"></i>
                            </div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--spark-text-primary, #0f172a);"><?= lang('template_quotes'); ?></h4>
                        </div>
                        <span class="badge" style="background: var(--spark-primary-subtle, rgba(99, 102, 241, 0.15)); color: var(--spark-primary-hover, #4338ca); border-radius: 6px; padding: 4px 8px;"><?= lang('quotes'); ?></span>
                    </div>

                    <?= admin_form_open('system_settings/whatsapp_templates/quotes'); ?>
                    <div class="form-group" style="margin-bottom: 12px;">
                        <textarea name="whatsapp_body" class="form-control" rows="3" id="comment_quotes" placeholder="<?= lang('template_body_placeholder'); ?>" style="font-size: 13.5px;"><?= isset($_POST['whatsapp_body']) ? html_entity_decode($_POST['whatsapp_body']) : html_entity_decode($quotes); ?></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <input type="submit" name="submit" class="spark-btn-whatsapp-primary" value="<?= lang('save_template'); ?>" style="padding: 8px 20px; font-size: 13px;" />
                    </div>
                    <?= form_close(); ?>
                </div>

                <!-- Template 3: Award Points -->
                <div class="spark-whatsapp-tpl-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #f59e0b;">
                                <i class="fa fa-star"></i>
                            </div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--spark-text-primary, #0f172a);"><?= lang('template_award_points'); ?></h4>
                        </div>
                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #b45309; border-radius: 6px; padding: 4px 8px;"><?= lang('award_points'); ?></span>
                    </div>

                    <?= admin_form_open('system_settings/whatsapp_templates/award_points'); ?>
                    <div class="form-group" style="margin-bottom: 12px;">
                        <textarea name="whatsapp_body" class="form-control" rows="3" id="comment_award" placeholder="<?= lang('template_body_placeholder'); ?>" style="font-size: 13.5px;"><?= isset($_POST['whatsapp_body']) ? html_entity_decode($_POST['whatsapp_body']) : html_entity_decode($award_points); ?></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <input type="submit" name="submit" class="spark-btn-whatsapp-primary" value="<?= lang('save_template'); ?>" style="padding: 8px 20px; font-size: 13px;" />
                    </div>
                    <?= form_close(); ?>
                </div>

                <!-- Template 4: Reverse Award Points -->
                <div class="spark-whatsapp-tpl-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239, 68, 68, 0.15); display: flex; align-items: center; justify-content: center; color: #ef4444;">
                                <i class="fa fa-undo"></i>
                            </div>
                            <h4 style="font-size: 15px; font-weight: 800; margin: 0; color: var(--spark-text-primary, #0f172a);"><?= lang('template_reverse_award_points'); ?></h4>
                        </div>
                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #b91c1c; border-radius: 6px; padding: 4px 8px;"><?= lang('award_points'); ?></span>
                    </div>

                    <?= admin_form_open('system_settings/whatsapp_templates/reverse_award_points'); ?>
                    <div class="form-group" style="margin-bottom: 12px;">
                        <textarea name="whatsapp_body" class="form-control" rows="3" id="comment_reverse" placeholder="<?= lang('template_body_placeholder'); ?>" style="font-size: 13.5px;"><?= isset($_POST['whatsapp_body']) ? html_entity_decode($_POST['whatsapp_body']) : html_entity_decode($reverse_award_points); ?></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <input type="submit" name="submit" class="spark-btn-whatsapp-primary" value="<?= lang('save_template'); ?>" style="padding: 8px 20px; font-size: 13px;" />
                    </div>
                    <?= form_close(); ?>
                </div>
            </div>

            <!-- Short Tags Column -->
            <div class="col-md-4 col-sm-12">
                <div class="spark-whatsapp-card">
                    <div class="spark-whatsapp-card-title">
                        <i class="fa fa-code"></i>
                        <span><?= lang('short_tags'); ?></span>
                    </div>
                    <p class="text-muted" style="font-size: 12px; margin-bottom: 14px;"><?= lang('short_tags_tip'); ?></p>

                    <div style="margin-bottom: 16px;">
                        <strong style="font-size: 13px; display: block; margin-bottom: 6px; color: var(--spark-text-primary, #0f172a);"><?= lang('general_tags'); ?></strong>
                        <div>
                            <span class="spark-whatsapp-tag-pill">{customer}</span>
                            <span class="spark-whatsapp-tag-pill">{site_name}</span>
                            <span class="spark-whatsapp-tag-pill">{order_link}</span>
                            <span class="spark-whatsapp-tag-pill">{default_currency}</span>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <strong style="font-size: 13px; display: block; margin-bottom: 6px; color: var(--spark-text-primary, #0f172a);"><?= lang('sales_tags'); ?></strong>
                        <div>
                            <span class="spark-whatsapp-tag-pill">{customer}</span>
                            <span class="spark-whatsapp-tag-pill">{sale_reference}</span>
                            <span class="spark-whatsapp-tag-pill">{grand_total}</span>
                            <span class="spark-whatsapp-tag-pill">{order_link}</span>
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <strong style="font-size: 13px; display: block; margin-bottom: 6px; color: var(--spark-text-primary, #0f172a);"><?= lang('quotes_tags'); ?></strong>
                        <div>
                            <span class="spark-whatsapp-tag-pill">{customer}</span>
                            <span class="spark-whatsapp-tag-pill">{quote_reference}</span>
                            <span class="spark-whatsapp-tag-pill">{grand_total}</span>
                            <span class="spark-whatsapp-tag-pill">{order_link}</span>
                        </div>
                    </div>

                    <div>
                        <strong style="font-size: 13px; display: block; margin-bottom: 6px; color: var(--spark-text-primary, #0f172a);"><?= lang('award_points_tags'); ?></strong>
                        <div>
                            <span class="spark-whatsapp-tag-pill">{customer}</span>
                            <span class="spark-whatsapp-tag-pill">{points}</span>
                            <span class="spark-whatsapp-tag-pill">{award_points}</span>
                            <span class="spark-whatsapp-tag-pill">{reverse_award_points}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
