<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('abouts')) {
            Schema::create('abouts', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('content')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('abouts_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('abouts_updated_by_id_fk');
                $table->string('locale')->nullable();
            });
        }

        if (!Schema::hasTable('actions')) {
            Schema::create('actions', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('execute_at', 6)->nullable();
                $table->string('mode')->nullable();
                $table->integer('entity_id')->nullable();
                $table->string('entity_slug')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('actions_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('actions_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('addresses_v2')) {
            Schema::create('addresses_v2', function (Blueprint $table) {
                $table->increments('id');
                $table->string('street')->nullable();
                $table->string('housenumber')->nullable();
                $table->string('city')->nullable();
                $table->string('zip')->nullable();
            });
        }

        if (!Schema::hasTable('admin_has_permissions')) {
            Schema::create('admin_has_permissions', function (Blueprint $table) {
                $table->unsignedInteger('permission_id');
                $table->string('model_type');
                $table->unsignedInteger('model_id');

                $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
                $table->primary(['permission_id', 'model_id', 'model_type']);
            });
        }

        if (!Schema::hasTable('admin_has_roles')) {
            Schema::create('admin_has_roles', function (Blueprint $table) {
                $table->unsignedInteger('role_id');
                $table->string('model_type');
                $table->unsignedInteger('model_id');

                $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
                $table->primary(['role_id', 'model_id', 'model_type']);
            });
        }

        if (!Schema::hasTable('admin_permissions')) {
            Schema::create('admin_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        }

        if (!Schema::hasTable('admin_roles')) {
            Schema::create('admin_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        }

        if (!Schema::hasTable('admin_users')) {
            Schema::create('admin_users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('firstname')->nullable();
                $table->string('lastname')->nullable();
                $table->string('username')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('reset_password_token')->nullable();
                $table->string('registration_token')->nullable();
                $table->boolean('is_active')->nullable();
                $table->boolean('blocked')->nullable();
                $table->string('prefered_language')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('admin_users_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('admin_users_updated_by_id_fk');
                $table->unsignedInteger('role_id')->nullable();
                $table->string('remember_token')->nullable();
            });
        }

        if (!Schema::hasTable('challenge_images')) {
            Schema::create('challenge_images', function (Blueprint $table) {
                $table->increments('id');
                $table->string('uuid')->nullable()->unique();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('challenge_images_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('challenge_images_updated_by_id_fk');
                $table->string('title')->nullable();
                $table->unsignedInteger('image_id')->nullable()->index('fk_challenges_images_image');
            });
        }

        if (!Schema::hasTable('challenge_templates')) {
            Schema::create('challenge_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->boolean('is_public')->nullable();
                $table->string('interval')->nullable();
                $table->string('kind')->nullable();
                $table->string('goal_type')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('challenge_templates_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('challenge_templates_updated_by_id_fk');
                $table->longText('teaser_text')->nullable();
                $table->unsignedInteger('partner_id')->nullable()->index('fk_challenge_templates_partner');
                $table->unsignedInteger('image_id')->nullable()->index('fk_challenge_templates_challenge_image');
            });
        }

        if (!Schema::hasTable('challenge_templates_image_links')) {
            Schema::create('challenge_templates_image_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('challenge_template_id')->nullable()->index('challenge_templates_image_links_fk');
                $table->unsignedInteger('challenge_image_id')->nullable()->index('challenge_templates_image_links_inv_fk');
                $table->unsignedInteger('challenge_template_order')->nullable()->index('challenge_templates_image_links_order_inv_fk');

                $table->unique(['challenge_template_id', 'challenge_image_id'], 'challenge_templates_image_links_unique');
            });
        }

        if (!Schema::hasTable('challenges')) {
            Schema::create('challenges', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->string('interval')->nullable();
                $table->boolean('is_public')->nullable();
                $table->string('kind')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('challenges_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('challenges_updated_by_id_fk');
                $table->string('locale')->nullable();
                $table->string('goal_type')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->integer('views')->nullable();
                $table->longText('teaser_text')->nullable();
                $table->boolean('trophy_processed')->nullable();
                $table->unsignedInteger('image_id')->nullable();
                $table->unsignedInteger('author_id')->nullable()->index('fk_challenges_author');
                $table->unsignedInteger('region_id')->nullable()->index('fk_challenges_region');
                $table->unsignedInteger('partner_id')->nullable()->index('fk_challenges_partner');
                $table->unsignedInteger('collective_goal_amount')->nullable();
            });
        }

        if (!Schema::hasTable('challenges_goal_type_measurements')) {
            Schema::create('challenges_goal_type_measurements', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('value')->nullable();
                $table->string('unit')->nullable();
                $table->unsignedInteger('challenge_id')->nullable()->index('fk_challenges_goal_type_measurements');
                $table->unsignedInteger('challenge_template_id')->nullable()->index('fk_challenges_goal_type_measurements_challenge_template');
            });
        }

        if (!Schema::hasTable('challenges_goal_type_steps')) {
            Schema::create('challenges_goal_type_steps', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('count')->nullable();
                $table->unsignedInteger('challenge_id')->nullable()->index('fk_challenges_goal_type_steps_challenge');
                $table->unsignedInteger('challenge_template_id')->nullable()->index('fk_challenges_goal_type_steps_challenge_template');
            });
        }

        if (!Schema::hasTable('contact_inquiries')) {
            Schema::create('contact_inquiries', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->longText('message')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('contact_inquiries_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('contact_inquiries_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('dashboard')) {
            Schema::create('dashboard', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('dashboard_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('dashboard_updated_by_id_fk');
                $table->string('locale')->nullable();
            });
        }

        if (!Schema::hasTable('files')) {
            Schema::create('files', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable()->index('upload_files_name_index');
                $table->longText('alternative_text')->nullable();
                $table->string('caption')->nullable();
                $table->integer('width')->nullable();
                $table->integer('height')->nullable();
                $table->longText('formats')->nullable();
                $table->string('hash')->nullable();
                $table->string('ext')->nullable()->index('upload_files_ext_index');
                $table->string('mime')->nullable();
                $table->decimal('size', 10)->nullable()->index('upload_files_size_index');
                $table->string('url')->nullable();
                $table->string('preview_url')->nullable();
                $table->string('provider')->nullable();
                $table->longText('provider_metadata')->nullable();
                $table->string('folder_path')->nullable()->index('upload_files_folder_path_index');
                $table->dateTime('created_at', 6)->nullable()->index('upload_files_created_at_index');
                $table->dateTime('updated_at', 6)->nullable()->index('upload_files_updated_at_index');
                $table->unsignedInteger('created_by_id')->nullable()->index('files_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('files_updated_by_id_fk');
                $table->string('uuid')->nullable();
            });
        }

        if (!Schema::hasTable('files_folder_links')) {
            Schema::create('files_folder_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('file_id')->nullable()->index('files_folder_links_fk');
                $table->unsignedInteger('folder_id')->nullable()->index('files_folder_links_inv_fk');
                $table->unsignedInteger('file_order')->nullable()->index('files_folder_links_order_inv_fk');

                $table->unique(['file_id', 'folder_id'], 'files_folder_links_unique');
            });
        }

        if (!Schema::hasTable('files_related_morphs')) {
            Schema::create('files_related_morphs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('file_id')->nullable()->index('files_related_morphs_fk');
                $table->unsignedInteger('related_id')->nullable();
                $table->string('related_type')->nullable();
                $table->string('field')->nullable();
                $table->unsignedInteger('order')->nullable();
            });
        }

        if (!Schema::hasTable('i18n_locale')) {
            Schema::create('i18n_locale', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('code')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('i18n_locale_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('i18n_locale_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('joined_challenges')) {
            Schema::create('joined_challenges', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('joined_challenges_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('joined_challenges_updated_by_id_fk');
                $table->unsignedInteger('challenge_id')->nullable()->index('fk_joined_challenges_challenge_id');
                $table->unsignedInteger('user_id')->nullable()->index('fk_joined_challenges_user_id');
            });
        }

        if (!Schema::hasTable('joined_challenges_answers')) {
            Schema::create('joined_challenges_answers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('answer')->nullable();
                $table->integer('day_index')->nullable();
                $table->unsignedInteger('joined_challenge_id')->nullable()->index('fk_joined_challenges_answers_challenge_id');
            });
        }

        if (!Schema::hasTable('legal_notices')) {
            Schema::create('legal_notices', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('content')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('legal_notices_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('legal_notices_updated_by_id_fk');
                $table->string('locale')->nullable();
            });
        }

        if (!Schema::hasTable('locations_v2')) {
            Schema::create('locations_v2', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
            });
        }

        if (!Schema::hasTable('news_entries')) {
            Schema::create('news_entries', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->date('date')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('news_entries_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('news_entries_updated_by_id_fk');
                $table->string('locale')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->longText('teaser')->nullable();
                $table->string('type')->nullable();
                $table->integer('views')->nullable();
                $table->unsignedInteger('region_id')->nullable()->index('fk_news_entries_region');
                $table->unsignedInteger('image_id')->nullable()->index('fk_news_entries_image');
            });
        }

        if (!Schema::hasTable('news_entries_tags_links')) {
            Schema::create('news_entries_tags_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('news_entry_id')->nullable()->index('news_entries_tags_links_fk');
                $table->unsignedInteger('news_tag_id')->nullable()->index('news_entries_tags_links_inv_fk');
                $table->unsignedInteger('news_tag_order')->nullable()->index('news_entries_tags_links_order_fk');

                $table->unique(['news_entry_id', 'news_tag_id'], 'news_entries_tags_links_unique');
            });
        }

        if (!Schema::hasTable('news_entries_users_read_links')) {
            Schema::create('news_entries_users_read_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('news_entry_id')->nullable()->index('news_entries_users_read_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('news_entries_users_read_links_inv_fk');
                $table->unsignedInteger('user_order')->nullable()->index('news_entries_users_read_links_order_fk');

                $table->unique(['news_entry_id', 'user_id'], 'news_entries_users_read_links_unique');
            });
        }

        if (!Schema::hasTable('news_tags')) {
            Schema::create('news_tags', function (Blueprint $table) {
                $table->increments('id');
                $table->string('value')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('news_tags_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('news_tags_updated_by_id_fk');
                $table->string('locale')->nullable();
                $table->string('uuid')->nullable()->unique();
            });
        }

        if (!Schema::hasTable('offers')) {
            Schema::create('offers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->longText('summary')->nullable();
                $table->longText('description')->nullable();
                $table->decimal('discount', 10)->nullable();
                $table->string('store_type')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('offers_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('offers_updated_by_id_fk');
                $table->string('locale')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->date('valid_from')->nullable();
                $table->date('valid_until')->nullable();
                $table->string('generic_voucher')->nullable();
                $table->longText('individual_vouchers')->nullable();
                $table->boolean('is_regional')->nullable();
                $table->integer('redeem_interval')->nullable();
                $table->integer('views')->nullable();
                $table->unsignedInteger('region_id')->nullable()->index('fk_offers_region');
                $table->unsignedInteger('location_id')->nullable()->index('fk_offers_location');
                $table->unsignedInteger('address_id')->nullable()->index('fk_offers_address');
                $table->unsignedInteger('image_id')->nullable()->index('fk_offers_image');
                $table->string('url')->nullable();
            });
        }

        if (!Schema::hasTable('partner_inquiries')) {
            Schema::create('partner_inquiries', function (Blueprint $table) {
                $table->increments('id');
                $table->string('product')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->longText('message')->nullable();
                $table->string('phone')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('partner_inquiries_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('partner_inquiries_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->string('url')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('partners_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('partners_updated_by_id_fk');
                $table->string('uuid')->nullable()->unique();
                $table->longText('description')->nullable();
                $table->unsignedInteger('logo_id')->nullable()->index('fk_partners_logo');
            });
        }

        if (!Schema::hasTable('partnerships')) {
            Schema::create('partnerships', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('content')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('partnerships_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('partnerships_updated_by_id_fk');
                $table->string('locale')->nullable();
            });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('tokenable_type');
                $table->unsignedBigInteger('tokenable_id');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['tokenable_type', 'tokenable_id']);
            });
        }

        if (!Schema::hasTable('privacy_policies')) {
            Schema::create('privacy_policies', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('content')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('privacy_policies_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('privacy_policies_updated_by_id_fk');
                $table->string('locale')->nullable();
            });
        }

        if (!Schema::hasTable('project_goal_fundings_v2')) {
            Schema::create('project_goal_fundings_v2', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('current_amount')->nullable();
                $table->integer('total_amount')->nullable();
            });
        }

        if (!Schema::hasTable('project_goal_involvements_v2')) {
            Schema::create('project_goal_involvements_v2', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('max_participants')->nullable();
                $table->unsignedInteger('current_participants')->nullable();
            });
        }

        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->longText('summary')->nullable();
                $table->longText('description')->nullable();
                $table->dateTime('start_date', 6)->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('projects_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('projects_updated_by_id_fk');
                $table->string('locale')->nullable();
                $table->string('goal_type')->nullable();
                $table->string('phase')->nullable();
                $table->longText('conclusion')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->boolean('trophy_processed')->nullable();
                $table->unsignedInteger('partner_id')->nullable()->index('fk_projects_partner');
                $table->unsignedInteger('region_id')->nullable()->index('fk_projects_region');
                $table->unsignedInteger('location_id')->nullable()->index('fk_projects_location');
                $table->unsignedInteger('goal_involvement_id')->nullable()->index('fk_projects_goal_involvements');
                $table->unsignedInteger('goal_funding_id')->nullable()->index('fk_projects_goal_funding');
                $table->unsignedInteger('image_id')->nullable()->index('fk_projects_image');
                $table->unsignedInteger('teaser_image_id')->nullable()->index('fk_projects_teaser_image');
            });
        }

        if (!Schema::hasTable('projects_related_projects_links')) {
            Schema::create('projects_related_projects_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('project_id')->nullable()->index('projects_related_projects_links_fk');
                $table->unsignedInteger('inv_project_id')->nullable()->index('projects_related_projects_links_inv_fk');
                $table->unsignedInteger('project_order')->nullable()->index('projects_related_projects_links_order_fk');

                $table->unique(['project_id', 'inv_project_id'], 'projects_related_projects_links_unique');
            });
        }

        if (!Schema::hasTable('projects_users_favorited_links')) {
            Schema::create('projects_users_favorited_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('project_id')->nullable()->index('projects_users_favorited_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('projects_users_favorited_links_inv_fk');
                $table->unsignedInteger('user_order')->nullable()->index('projects_users_favorited_links_order_fk');
                $table->unsignedInteger('project_order')->nullable()->index('projects_users_favorited_links_order_inv_fk');

                $table->unique(['project_id', 'user_id'], 'projects_users_favorited_links_unique');
            });
        }

        if (!Schema::hasTable('projects_users_joined_links')) {
            Schema::create('projects_users_joined_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('project_id')->nullable()->index('projects_users_joined_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('projects_users_joined_links_inv_fk');
                $table->unsignedInteger('user_order')->nullable()->index('projects_users_joined_links_order_fk');
                $table->unsignedInteger('project_order')->nullable()->index('projects_users_joined_links_order_inv_fk');

                $table->unique(['project_id', 'user_id'], 'projects_users_joined_links_unique');
            });
        }

        if (!Schema::hasTable('quiz_answers')) {
            Schema::create('quiz_answers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('option')->nullable();
                $table->longText('text')->nullable();
                $table->unsignedInteger('quiz_question_id')->nullable()->index('fk_quiz_questions_quiz_answers');
            });
        }

        if (!Schema::hasTable('quiz_questions')) {
            Schema::create('quiz_questions', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('question');
                $table->longText('explanation')->nullable();
                $table->string('correct_answer');
                $table->string('uuid')->nullable()->unique();
                $table->string('locale')->nullable();
                $table->boolean('is_filler')->nullable();
                $table->unsignedInteger('region_id')->nullable()->index('fk_quiz_questions_region');
            });
        }

        if (!Schema::hasTable('quiz_responses_v2')) {
            Schema::create('quiz_responses_v2', function (Blueprint $table) {
                $table->string('answer')->nullable();
                $table->dateTime('date', 6)->nullable();
                $table->unsignedInteger('quiz_id')->nullable()->index('fk_quiz_responses_quiz_id');
                $table->unsignedInteger('user_id')->nullable()->index('fk_quiz_responses_user_id');
            });
        }

        if (!Schema::hasTable('quiz_streaks')) {
            Schema::create('quiz_streaks', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('participation_streak')->nullable();
                $table->integer('max_correct_answer_streak')->nullable();
                $table->integer('correct_answer_streak')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('quiz_streaks_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('quiz_streaks_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('quizzes')) {
            Schema::create('quizzes', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('quizzes_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('quizzes_updated_by_id_fk');
                $table->string('uuid')->nullable()->unique();
                $table->date('date')->nullable();
                $table->unsignedInteger('quiz_question_id')->nullable()->index('fk_quizzes_quiz_questions');
            });
        }

        if (!Schema::hasTable('redeemed-voucher')) {
            Schema::create('redeemed-voucher', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('redeemed-voucher_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('redeemed-voucher_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('regions_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('regions_updated_by_id_fk');
                $table->string('uuid')->nullable()->unique();
                $table->boolean('is_public')->nullable()->default(true);
                $table->boolean('is_supraregional')->nullable();
            });
        }

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedInteger('permission_id');
                $table->unsignedInteger('role_id')->index('role_has_permissions_role_id_foreign');

                $table->primary(['permission_id', 'role_id']);
            });
        }

        if (!Schema::hasTable('sponsor_memberships')) {
            Schema::create('sponsor_memberships', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('content')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('sponsor_memberships_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('sponsor_memberships_updated_by_id_fk');
                $table->string('locale')->nullable();
            });
        }

        if (!Schema::hasTable('surveys')) {
            Schema::create('surveys', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->boolean('finished')->nullable();
                $table->string('survey_url')->nullable();
                $table->string('evaluation_url')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->boolean('trophy_processed')->nullable();
                $table->string('target')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('surveys_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('surveys_updated_by_id_fk');
                $table->string('locale')->nullable();
            });
        }

        if (!Schema::hasTable('surveys_evaluated_by_links')) {
            Schema::create('surveys_evaluated_by_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('survey_id')->nullable()->index('surveys_evaluated_by_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('surveys_evaluated_by_links_inv_fk');
                $table->unsignedInteger('user_order')->nullable()->index('surveys_evaluated_by_links_order_fk');

                $table->unique(['survey_id', 'user_id'], 'surveys_evaluated_by_links_unique');
            });
        }

        if (!Schema::hasTable('surveys_participants_links')) {
            Schema::create('surveys_participants_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('survey_id')->nullable()->index('surveys_participants_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('surveys_participants_links_inv_fk');
                $table->unsignedInteger('user_order')->nullable()->index('surveys_participants_links_order_fk');

                $table->unique(['survey_id', 'user_id'], 'surveys_participants_links_unique');
            });
        }

        if (!Schema::hasTable('trophies')) {
            Schema::create('trophies', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->string('kind')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('trophies_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('trophies_updated_by_id_fk');
                $table->string('locale')->nullable();
                $table->integer('amount')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->unsignedInteger('challenge_id')->nullable()->index('fk_trophies_challenge');
                $table->unsignedInteger('image_id')->nullable()->index('fk_trophies_image');
            });
        }

        if (!Schema::hasTable('up_permissions')) {
            Schema::create('up_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('action')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('up_permissions_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('up_permissions_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('up_permissions_role_links')) {
            Schema::create('up_permissions_role_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('permission_id')->nullable()->index('up_permissions_role_links_fk');
                $table->unsignedInteger('role_id')->nullable()->index('up_permissions_role_links_inv_fk');
                $table->unsignedInteger('permission_order')->nullable()->index('up_permissions_role_links_order_inv_fk');

                $table->unique(['permission_id', 'role_id'], 'up_permissions_role_links_unique');
            });
        }

        if (!Schema::hasTable('up_roles')) {
            Schema::create('up_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('description')->nullable();
                $table->string('type')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('up_roles_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('up_roles_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('up_users')) {
            Schema::create('up_users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username')->nullable();
                $table->string('email')->nullable();
                $table->string('provider')->nullable();
                $table->string('password')->nullable();
                $table->string('reset_password_token')->nullable();
                $table->string('confirmation_token')->nullable();
                $table->boolean('confirmed')->nullable();
                $table->boolean('blocked')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('up_users_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('up_users_updated_by_id_fk');
                $table->string('referral_code')->nullable();
                $table->string('uuid')->nullable();
                $table->boolean('accepts_surveys')->nullable();
                $table->boolean('is_supporter')->nullable();
                $table->integer('referral_count')->nullable();
                $table->unsignedInteger('avatar_id')->nullable()->index('fk_up_users_avatar');
                $table->unsignedInteger('region_id')->nullable()->index('fk_up_users_region');
                $table->unsignedInteger('role_id')->nullable()->index('fk_up_users_role');
                $table->boolean('is_anonymous')->nullable();
                $table->unsignedInteger('quiz_streak_id')->nullable()->index('fk_up_users_quiz_streak');

                $table->unique(['uuid', 'is_anonymous']);
            });
        }

        if (!Schema::hasTable('up_users_favorited_news_links')) {
            Schema::create('up_users_favorited_news_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->nullable()->index('up_users_favorited_news_links_fk');
                $table->unsignedInteger('news_entry_id')->nullable()->index('up_users_favorited_news_links_inv_fk');
                $table->unsignedInteger('news_entry_order')->nullable()->index('up_users_favorited_news_links_order_fk');
                $table->unsignedInteger('user_order')->nullable()->index('up_users_favorited_news_links_order_inv_fk');

                $table->unique(['user_id', 'news_entry_id'], 'up_users_favorited_news_links_unique');
            });
        }

        if (!Schema::hasTable('upload_folders')) {
            Schema::create('upload_folders', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->integer('path_id')->nullable()->unique('upload_folders_path_id_index');
                $table->string('path')->nullable()->unique('upload_folders_path_index');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('upload_folders_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('upload_folders_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('upload_folders_parent_links')) {
            Schema::create('upload_folders_parent_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('folder_id')->nullable()->index('upload_folders_parent_links_fk');
                $table->unsignedInteger('inv_folder_id')->nullable()->index('upload_folders_parent_links_inv_fk');
                $table->unsignedInteger('folder_order')->nullable()->index('upload_folders_parent_links_order_inv_fk');

                $table->unique(['folder_id', 'inv_folder_id'], 'upload_folders_parent_links_unique');
            });
        }

        if (!Schema::hasTable('user_avatars')) {
            Schema::create('user_avatars', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->dateTime('published_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('user_avatars_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('user_avatars_updated_by_id_fk');
                $table->string('locale')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->unsignedInteger('image_id')->nullable()->index('fk_user_avatars_image');
            });
        }

        if (!Schema::hasTable('user_follows_v2')) {
            Schema::create('user_follows_v2', function (Blueprint $table) {
                $table->boolean('is_request')->nullable();
                $table->string('uuid')->nullable();
                $table->unsignedInteger('followed_user_id')->nullable()->index('fk_user_follows_followed_user_id');
                $table->unsignedInteger('follows_user_id')->nullable()->index('fk_user_follows_follows_user_id');
            });
        }

        if (!Schema::hasTable('user_trophies_v2')) {
            Schema::create('user_trophies_v2', function (Blueprint $table) {
                $table->boolean('notified')->nullable();
                $table->dateTime('date', 6)->nullable();
                $table->unsignedInteger('trophy_id')->nullable()->index('fk_trophies_trophy_id');
                $table->unsignedInteger('user_id')->nullable()->index('fk_trophies_user_id');
            });
        }

        if (!Schema::hasTable('voucher_redemptions')) {
            Schema::create('voucher_redemptions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code')->nullable();
                $table->date('redeemed_at')->nullable();
                $table->string('anonymous_user_id')->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('voucher_redemptions_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('voucher_redemptions_updated_by_id_fk');
                $table->unsignedInteger('redeemer_id')->nullable()->index('fk_voucher_redemptions_redeemer');
                $table->unsignedInteger('offer_id')->nullable()->index('fk_voucher_redemptions_offer');
            });
        }

        if (!Schema::hasTable('zz_admin_permissions_role_links')) {
            Schema::create('zz_admin_permissions_role_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('permission_id')->nullable()->index('admin_permissions_role_links_fk');
                $table->unsignedInteger('role_id')->nullable()->index('admin_permissions_role_links_inv_fk');
                $table->unsignedInteger('permission_order')->nullable()->index('admin_permissions_role_links_order_inv_fk');

                $table->unique(['permission_id', 'role_id'], 'admin_permissions_role_links_unique');
            });
        }

        if (!Schema::hasTable('zz_admin_users_roles_links')) {
            Schema::create('zz_admin_users_roles_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->nullable()->index('admin_users_roles_links_fk');
                $table->unsignedInteger('role_id')->nullable()->index('admin_users_roles_links_inv_fk');
                $table->unsignedInteger('role_order')->nullable()->index('admin_users_roles_links_order_fk');
                $table->unsignedInteger('user_order')->nullable()->index('admin_users_roles_links_order_inv_fk');

                $table->unique(['user_id', 'role_id'], 'admin_users_roles_links_unique');
            });
        }

        if (!Schema::hasTable('zz_challenge_templates_components')) {
            Schema::create('zz_challenge_templates_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('challenge_templates_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('challenge_templates_component_type_index');
                $table->string('field')->nullable()->index('challenge_templates_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'challenge_templates_unique');
            });
        }

        if (!Schema::hasTable('zz_challenge_templates_partner_links')) {
            Schema::create('zz_challenge_templates_partner_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('challenge_template_id')->nullable()->index('challenge_templates_partner_links_fk');
                $table->unsignedInteger('partner_id')->nullable()->index('challenge_templates_partner_links_inv_fk');

                $table->unique(['challenge_template_id', 'partner_id'], 'challenge_templates_partner_links_unique');
            });
        }

        if (!Schema::hasTable('zz_challenges_author_links')) {
            Schema::create('zz_challenges_author_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('challenge_id')->nullable()->index('challenges_author_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('challenges_author_links_inv_fk');

                $table->unique(['challenge_id', 'user_id'], 'challenges_author_links_unique');
            });
        }

        if (!Schema::hasTable('zz_challenges_components')) {
            Schema::create('zz_challenges_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('challenges_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('challenges_component_type_index');
                $table->string('field')->nullable()->index('challenges_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'challenges_unique');
            });
        }

        if (!Schema::hasTable('zz_challenges_image_links')) {
            Schema::create('zz_challenges_image_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('challenge_id')->nullable()->index('challenges_image_links_fk');
                $table->unsignedInteger('challenge_image_id')->nullable()->index('challenges_image_links_inv_fk');

                $table->unique(['challenge_id', 'challenge_image_id'], 'challenges_image_links_unique');
            });
        }

        if (!Schema::hasTable('zz_challenges_partner_links')) {
            Schema::create('zz_challenges_partner_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('challenge_id')->nullable()->index('challenges_partner_links_fk');
                $table->unsignedInteger('partner_id')->nullable()->index('challenges_partner_links_inv_fk');

                $table->unique(['challenge_id', 'partner_id'], 'challenges_partner_links_unique');
            });
        }

        if (!Schema::hasTable('zz_challenges_region_links')) {
            Schema::create('zz_challenges_region_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('challenge_id')->nullable()->index('challenges_region_links_fk');
                $table->unsignedInteger('region_id')->nullable()->index('challenges_region_links_inv_fk');

                $table->unique(['challenge_id', 'region_id'], 'challenges_region_links_unique');
            });
        }

        if (!Schema::hasTable('zz_challenges_users_joined_links')) {
            Schema::create('zz_challenges_users_joined_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('challenge_id')->nullable()->index('challenges_users_joined_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('challenges_users_joined_links_inv_fk');
                $table->unsignedInteger('user_order')->nullable()->index('challenges_users_joined_links_order_fk');

                $table->unique(['challenge_id', 'user_id'], 'challenges_users_joined_links_unique');
            });
        }

        if (!Schema::hasTable('zz_components_misc_anonymous_user_ids')) {
            Schema::create('zz_components_misc_anonymous_user_ids', function (Blueprint $table) {
                $table->increments('id');
                $table->string('anonymous_user_id')->nullable();
            });
        }

        if (!Schema::hasTable('zz_components_project_coordinates')) {
            Schema::create('zz_components_project_coordinates', function (Blueprint $table) {
                $table->increments('id');
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
            });
        }

        if (!Schema::hasTable('zz_components_project_locations_components')) {
            Schema::create('zz_components_project_locations_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('components_project_locations_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('components_project_locations_component_type_index');
                $table->string('field')->nullable()->index('components_project_locations_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'components_project_locations_unique');
            });
        }

        if (!Schema::hasTable('zz_components_user_trophies')) {
            Schema::create('zz_components_user_trophies', function (Blueprint $table) {
                $table->increments('id');
                $table->date('date')->nullable();
            });
        }

        if (!Schema::hasTable('zz_components_user_trophies_trophy_links')) {
            Schema::create('zz_components_user_trophies_trophy_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('trophies_id')->nullable()->index('components_user_trophies_trophy_links_fk');
                $table->unsignedInteger('trophy_id')->nullable()->index('components_user_trophies_trophy_links_inv_fk');

                $table->unique(['trophies_id', 'trophy_id'], 'components_user_trophies_trophy_links_unique');
            });
        }

        if (!Schema::hasTable('zz_joined_challenges_challenge_links')) {
            Schema::create('zz_joined_challenges_challenge_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('joined_challenge_id')->nullable()->index('joined_challenges_challenge_links_fk');
                $table->unsignedInteger('challenge_id')->nullable()->index('joined_challenges_challenge_links_inv_fk');
                $table->unsignedInteger('joined_challenge_order')->nullable()->index('joined_challenges_challenge_links_order_inv_fk');

                $table->unique(['joined_challenge_id', 'challenge_id'], 'joined_challenges_challenge_links_unique');
            });
        }

        if (!Schema::hasTable('zz_joined_challenges_components')) {
            Schema::create('zz_joined_challenges_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('joined_challenges_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('joined_challenges_component_type_index');
                $table->string('field')->nullable()->index('joined_challenges_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'joined_challenges_unique');
            });
        }

        if (!Schema::hasTable('zz_joined_challenges_user_links')) {
            Schema::create('zz_joined_challenges_user_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('joined_challenge_id')->nullable()->index('joined_challenges_user_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('joined_challenges_user_links_inv_fk');

                $table->unique(['joined_challenge_id', 'user_id'], 'joined_challenges_user_links_unique');
            });
        }

        if (!Schema::hasTable('zz_news_entries_components')) {
            Schema::create('zz_news_entries_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('news_entries_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('news_entries_component_type_index');
                $table->string('field')->nullable()->index('news_entries_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'news_entries_unique');
            });
        }

        if (!Schema::hasTable('zz_news_entries_region_links')) {
            Schema::create('zz_news_entries_region_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('news_entry_id')->nullable()->index('news_entries_region_links_fk');
                $table->unsignedInteger('region_id')->nullable()->index('news_entries_region_links_inv_fk');

                $table->unique(['news_entry_id', 'region_id'], 'news_entries_region_links_unique');
            });
        }

        if (!Schema::hasTable('zz_offers_components')) {
            Schema::create('zz_offers_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('offers_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('offers_component_type_index');
                $table->string('field')->nullable()->index('offers_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'offers_unique');
            });
        }

        if (!Schema::hasTable('zz_offers_region_links')) {
            Schema::create('zz_offers_region_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('offer_id')->nullable()->index('offers_region_links_fk');
                $table->unsignedInteger('region_id')->nullable()->index('offers_region_links_inv_fk');

                $table->unique(['offer_id', 'region_id'], 'offers_region_links_unique');
            });
        }

        if (!Schema::hasTable('zz_projects_components')) {
            Schema::create('zz_projects_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('projects_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('projects_component_type_index');
                $table->string('field')->nullable()->index('projects_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'projects_unique');
            });
        }

        if (!Schema::hasTable('zz_projects_partner_links')) {
            Schema::create('zz_projects_partner_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('project_id')->nullable()->index('projects_partner_links_fk');
                $table->unsignedInteger('partner_id')->nullable()->index('projects_partner_links_inv_fk');

                $table->unique(['project_id', 'partner_id'], 'projects_partner_links_unique');
            });
        }

        if (!Schema::hasTable('zz_projects_region_links')) {
            Schema::create('zz_projects_region_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('project_id')->nullable()->index('projects_region_links_fk');
                $table->unsignedInteger('region_id')->nullable()->index('projects_region_links_inv_fk');

                $table->unique(['project_id', 'region_id'], 'projects_region_links_unique');
            });
        }

        if (!Schema::hasTable('zz_quiz_responses')) {
            Schema::create('zz_quiz_responses', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('date', 6)->nullable();
                $table->string('answer')->nullable();
                $table->string('anonymous_user_id')->nullable();
                $table->string('uuid')->nullable()->unique('quiz-responses_uuid_unique');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('quiz-responses_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('quiz-responses_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('zz_quiz_responses_quiz_links')) {
            Schema::create('zz_quiz_responses_quiz_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('quiz_response_id')->nullable()->index('quiz_responses_quiz_links_fk');
                $table->unsignedInteger('quiz_id')->nullable()->index('quiz_responses_quiz_links_inv_fk');

                $table->unique(['quiz_response_id', 'quiz_id'], 'quiz_responses_quiz_links_unique');
            });
        }

        if (!Schema::hasTable('zz_quiz_responses_user_links')) {
            Schema::create('zz_quiz_responses_user_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('quiz_response_id')->nullable()->index('quiz_responses_user_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('quiz_responses_user_links_inv_fk');

                $table->unique(['quiz_response_id', 'user_id'], 'quiz_responses_user_links_unique');
            });
        }

        if (!Schema::hasTable('zz_quiz_streaks_user_links')) {
            Schema::create('zz_quiz_streaks_user_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('quiz_streak_id')->nullable()->index('quiz_streaks_user_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('quiz_streaks_user_links_inv_fk');

                $table->unique(['quiz_streak_id', 'user_id'], 'quiz_streaks_user_links_unique');
            });
        }

        if (!Schema::hasTable('zz_quizzes_components')) {
            Schema::create('zz_quizzes_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('quizzes_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('quizzes_component_type_index');
                $table->string('field')->nullable()->index('quizzes_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'quizzes_unique');
            });
        }

        if (!Schema::hasTable('zz_surveys_components')) {
            Schema::create('zz_surveys_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('surveys_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('surveys_component_type_index');
                $table->string('field')->nullable()->index('surveys_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'surveys_unique');
            });
        }

        if (!Schema::hasTable('zz_trophies_challenge_links')) {
            Schema::create('zz_trophies_challenge_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('trophy_id')->nullable()->index('trophies_challenge_links_fk');
                $table->unsignedInteger('challenge_id')->nullable()->index('trophies_challenge_links_inv_fk');

                $table->unique(['trophy_id', 'challenge_id'], 'trophies_challenge_links_unique');
            });
        }

        if (!Schema::hasTable('zz_up_users_avatar_links')) {
            Schema::create('zz_up_users_avatar_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->nullable()->index('up_users_avatar_links_fk');
                $table->unsignedInteger('user_avatar_id')->nullable()->index('up_users_avatar_links_inv_fk');

                $table->unique(['user_id', 'user_avatar_id'], 'up_users_avatar_links_unique');
            });
        }

        if (!Schema::hasTable('zz_up_users_components')) {
            Schema::create('zz_up_users_components', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('entity_id')->nullable()->index('up_users_entity_fk');
                $table->unsignedInteger('component_id')->nullable();
                $table->string('component_type')->nullable()->index('up_users_component_type_index');
                $table->string('field')->nullable()->index('up_users_field_index');
                $table->unsignedInteger('order')->nullable();

                $table->unique(['entity_id', 'component_id', 'field', 'component_type'], 'up_users_unique');
            });
        }

        if (!Schema::hasTable('zz_up_users_joined_challenges_links')) {
            Schema::create('zz_up_users_joined_challenges_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->nullable()->index('up_users_joined_challenges_links_fk');
                $table->unsignedInteger('joined_challenge_id')->nullable()->index('up_users_joined_challenges_links_inv_fk');
                $table->unsignedInteger('joined_challenge_order')->nullable()->index('up_users_joined_challenges_links_order_fk');

                $table->unique(['user_id', 'joined_challenge_id'], 'up_users_joined_challenges_links_unique');
            });
        }

        if (!Schema::hasTable('zz_up_users_region_links')) {
            Schema::create('zz_up_users_region_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->nullable()->index('up_users_region_links_fk');
                $table->unsignedInteger('region_id')->nullable()->index('up_users_region_links_inv_fk');

                $table->unique(['user_id', 'region_id'], 'up_users_region_links_unique');
            });
        }

        if (!Schema::hasTable('zz_up_users_role_links')) {
            Schema::create('zz_up_users_role_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->nullable()->index('up_users_role_links_fk');
                $table->unsignedInteger('role_id')->nullable()->index('up_users_role_links_inv_fk');
                $table->unsignedInteger('user_order')->nullable()->index('up_users_role_links_order_inv_fk');

                $table->unique(['user_id', 'role_id'], 'up_users_role_links_unique');
            });
        }

        if (!Schema::hasTable('zz_user_follows')) {
            Schema::create('zz_user_follows', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('is_request')->nullable();
                $table->string('uuid')->nullable()->unique('user_follows_uuid_unique');
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('user_follows_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('user_follows_updated_by_id_fk');
            });
        }

        if (!Schema::hasTable('zz_user_follows_followed_user_links')) {
            Schema::create('zz_user_follows_followed_user_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_follow_id')->nullable()->index('user_follows_followed_user_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('user_follows_followed_user_links_inv_fk');

                $table->unique(['user_follow_id', 'user_id'], 'user_follows_followed_user_links_unique');
            });
        }

        if (!Schema::hasTable('zz_user_follows_user_links')) {
            Schema::create('zz_user_follows_user_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_follow_id')->nullable()->index('user_follows_user_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('user_follows_user_links_inv_fk');

                $table->unique(['user_follow_id', 'user_id'], 'user_follows_user_links_unique');
            });
        }

        if (!Schema::hasTable('zz_user_trophies')) {
            Schema::create('zz_user_trophies', function (Blueprint $table) {
                $table->increments('id');
                $table->string('anonymous_user_id')->nullable();
                $table->dateTime('date', 6)->nullable();
                $table->dateTime('created_at', 6)->nullable();
                $table->dateTime('updated_at', 6)->nullable();
                $table->unsignedInteger('created_by_id')->nullable()->index('user_trophies_created_by_id_fk');
                $table->unsignedInteger('updated_by_id')->nullable()->index('user_trophies_updated_by_id_fk');
                $table->boolean('notified')->nullable();
            });
        }

        if (!Schema::hasTable('zz_user_trophies_trophy_links')) {
            Schema::create('zz_user_trophies_trophy_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_trophy_id')->nullable()->index('user_trophies_trophy_links_fk');
                $table->unsignedInteger('trophy_id')->nullable()->index('user_trophies_trophy_links_inv_fk');

                $table->unique(['user_trophy_id', 'trophy_id'], 'user_trophies_trophy_links_unique');
            });
        }

        if (!Schema::hasTable('zz_user_trophies_user_links')) {
            Schema::create('zz_user_trophies_user_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_trophy_id')->nullable()->index('user_trophies_user_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('user_trophies_user_links_inv_fk');

                $table->unique(['user_trophy_id', 'user_id'], 'user_trophies_user_links_unique');
            });
        }

        if (!Schema::hasTable('zz_voucher_redemptions_offer_links')) {
            Schema::create('zz_voucher_redemptions_offer_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('voucher_redemption_id')->nullable()->index('voucher_redemptions_offer_links_fk');
                $table->unsignedInteger('offer_id')->nullable()->index('voucher_redemptions_offer_links_inv_fk');
                $table->unsignedInteger('voucher_redemption_order')->nullable()->index('voucher_redemptions_offer_links_order_inv_fk');

                $table->unique(['voucher_redemption_id', 'offer_id'], 'voucher_redemptions_offer_links_unique');
            });
        }

        if (!Schema::hasTable('zz_voucher_redemptions_user_links')) {
            Schema::create('zz_voucher_redemptions_user_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('voucher_redemption_id')->nullable()->index('voucher_redemptions_user_links_fk');
                $table->unsignedInteger('user_id')->nullable()->index('voucher_redemptions_user_links_inv_fk');

                $table->unique(['voucher_redemption_id', 'user_id'], 'voucher_redemptions_user_links_unique');
            });
        }

        if (Schema::hasTable('abouts')) {
            Schema::table('abouts', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('abouts');
                if (!in_array('abouts_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'abouts_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('abouts_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'abouts_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('actions')) {
            Schema::table('actions', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('actions');
                if (!in_array('actions_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'actions_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('actions_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'actions_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('admin_has_permissions')) {
            Schema::table('admin_has_permissions', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('admin_has_permissions');
                if (!in_array('admin_has_permissions_permission_id_foreign', $foreignKeys))
                    $table->foreign(['permission_id'], 'admin_has_permissions_permission_id_foreign')->references(['id'])->on('admin_permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('admin_has_roles')) {
            Schema::table('admin_has_roles', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('admin_has_roles');
                if (!in_array('admin_has_roles_role_id_foreign', $foreignKeys))
                    $table->foreign(['role_id'], 'admin_has_roles_role_id_foreign')->references(['id'])->on('admin_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('admin_users')) {
            Schema::table('admin_users', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('admin_users');
                if (!in_array('admin_users_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'admin_users_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('admin_users_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'admin_users_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('challenge_images')) {
            Schema::table('challenge_images', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('challenge_images');
                if (!in_array('challenge_images_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'challenge_images_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('challenge_images_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'challenge_images_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_challenges_images_image', $foreignKeys))
                    $table->foreign(['image_id'], 'fk_challenges_images_image')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('challenge_templates')) {
            Schema::table('challenge_templates', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('challenge_templates');
                if (!in_array('challenge_templates_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'challenge_templates_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('challenge_templates_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'challenge_templates_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_challenge_templates_partner', $foreignKeys))
                    $table->foreign(['partner_id'], 'fk_challenge_templates_partner')->references(['id'])->on('partners')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('challenge_templates_image_links')) {
            Schema::table('challenge_templates_image_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('challenge_templates_image_links');
                if (!in_array('challenge_templates_image_links_fk', $foreignKeys))
                    $table->foreign(['challenge_template_id'], 'challenge_templates_image_links_fk')->references(['id'])->on('challenge_templates')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('challenge_templates_image_links_inv_fk', $foreignKeys))
                    $table->foreign(['challenge_image_id'], 'challenge_templates_image_links_inv_fk')->references(['id'])->on('challenge_images')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('challenges')) {
            Schema::table('challenges', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('challenges');
                if (!in_array('challenges_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'challenges_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('challenges_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'challenges_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_challenges_author', $foreignKeys))
                    $table->foreign(['author_id'], 'fk_challenges_author')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_challenges_partner', $foreignKeys))
                    $table->foreign(['partner_id'], 'fk_challenges_partner')->references(['id'])->on('partners')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_challenges_region', $foreignKeys))
                    $table->foreign(['region_id'], 'fk_challenges_region')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('challenges_goal_type_measurements')) {
            Schema::table('challenges_goal_type_measurements', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('challenges_goal_type_measurements');
                if (!in_array('fk_challenges_goal_type_measurements', $foreignKeys))
                    $table->foreign(['challenge_id'], 'fk_challenges_goal_type_measurements')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_challenges_goal_type_measurements_challenge_template', $foreignKeys))
                    $table->foreign(['challenge_template_id'], 'fk_challenges_goal_type_measurements_challenge_template')->references(['id'])->on('challenge_templates')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('challenges_goal_type_steps')) {
            Schema::table('challenges_goal_type_steps', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('challenges_goal_type_steps');
                if (!in_array('fk_challenges_goal_type_steps_challenge', $foreignKeys))
                    $table->foreign(['challenge_id'], 'fk_challenges_goal_type_steps_challenge')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_challenges_goal_type_steps_challenge_template', $foreignKeys))
                    $table->foreign(['challenge_template_id'], 'fk_challenges_goal_type_steps_challenge_template')->references(['id'])->on('challenge_templates')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('contact_inquiries')) {
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('contact_inquiries');
                if (!in_array('contact_inquiries_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'contact_inquiries_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('contact_inquiries_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'contact_inquiries_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('dashboard')) {
            Schema::table('dashboard', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('dashboard');
                if (!in_array('dashboard_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'dashboard_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('dashboard_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'dashboard_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('files')) {
            Schema::table('files', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('files');
                if (!in_array('files_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'files_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('files_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'files_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('files_folder_links')) {
            Schema::table('files_folder_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('files_folder_links');
                if (!in_array('files_folder_links_fk', $foreignKeys))
                    $table->foreign(['file_id'], 'files_folder_links_fk')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('files_folder_links_inv_fk', $foreignKeys))
                    $table->foreign(['folder_id'], 'files_folder_links_inv_fk')->references(['id'])->on('upload_folders')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('files_related_morphs')) {
            Schema::table('files_related_morphs', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('files_related_morphs');
                if (!in_array('files_related_morphs_fk', $foreignKeys))
                    $table->foreign(['file_id'], 'files_related_morphs_fk')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('i18n_locale')) {
            Schema::table('i18n_locale', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('i18n_locale');
                if (!in_array('i18n_locale_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'i18n_locale_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('i18n_locale_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'i18n_locale_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('joined_challenges')) {
            Schema::table('joined_challenges', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('joined_challenges');
                if (!in_array('fk_joined_challenges_challenge_id', $foreignKeys))
                    $table->foreign(['challenge_id'], 'fk_joined_challenges_challenge_id')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_joined_challenges_user_id', $foreignKeys))
                    $table->foreign(['user_id'], 'fk_joined_challenges_user_id')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('joined_challenges_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'joined_challenges_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('joined_challenges_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'joined_challenges_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('joined_challenges_answers')) {
            Schema::table('joined_challenges_answers', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('joined_challenges_answers');
                if (!in_array('fk_joined_challenges_answers_challenge_id', $foreignKeys))
                    $table->foreign(['joined_challenge_id'], 'fk_joined_challenges_answers_challenge_id')->references(['id'])->on('joined_challenges')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('legal_notices')) {
            Schema::table('legal_notices', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('legal_notices');
                if (!in_array('legal_notices_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'legal_notices_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('legal_notices_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'legal_notices_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('news_entries')) {
            Schema::table('news_entries', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('news_entries');
                if (!in_array('fk_news_entries_image', $foreignKeys))
                    $table->foreign(['image_id'], 'fk_news_entries_image')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_news_entries_region', $foreignKeys))
                    $table->foreign(['region_id'], 'fk_news_entries_region')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('news_entries_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'news_entries_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('news_entries_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'news_entries_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('news_entries_tags_links')) {
            Schema::table('news_entries_tags_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('news_entries_tags_links');
                if (!in_array('news_entries_tags_links_fk', $foreignKeys))
                    $table->foreign(['news_entry_id'], 'news_entries_tags_links_fk')->references(['id'])->on('news_entries')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('news_entries_tags_links_inv_fk', $foreignKeys))
                    $table->foreign(['news_tag_id'], 'news_entries_tags_links_inv_fk')->references(['id'])->on('news_tags')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('news_entries_users_read_links')) {
            Schema::table('news_entries_users_read_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('news_entries_users_read_links');
                if (!in_array('news_entries_users_read_links_fk', $foreignKeys))
                    $table->foreign(['news_entry_id'], 'news_entries_users_read_links_fk')->references(['id'])->on('news_entries')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('news_entries_users_read_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'news_entries_users_read_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('news_tags')) {
            Schema::table('news_tags', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('news_tags');
                if (!in_array('news_tags_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'news_tags_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('news_tags_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'news_tags_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('offers')) {
            Schema::table('offers', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('offers');
                if (!in_array('fk_offers_address', $foreignKeys))
                    $table->foreign(['address_id'], 'fk_offers_address')->references(['id'])->on('addresses_v2')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                if (!in_array('fk_offers_image', $foreignKeys))
                    $table->foreign(['image_id'], 'fk_offers_image')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_offers_location', $foreignKeys))
                    $table->foreign(['location_id'], 'fk_offers_location')->references(['id'])->on('locations_v2')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_offers_region', $foreignKeys))
                    $table->foreign(['region_id'], 'fk_offers_region')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('offers_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'offers_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('offers_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'offers_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('partner_inquiries')) {
            Schema::table('partner_inquiries', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('partner_inquiries');
                if (!in_array('partner_inquiries_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'partner_inquiries_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('partner_inquiries_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'partner_inquiries_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('partners')) {
            Schema::table('partners', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('partners');
                if (!in_array('fk_partners_logo', $foreignKeys))
                    $table->foreign(['logo_id'], 'fk_partners_logo')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('partners_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'partners_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('partners_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'partners_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('partnerships')) {
            Schema::table('partnerships', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('partnerships');
                if (!in_array('partnerships_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'partnerships_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('partnerships_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'partnerships_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('privacy_policies')) {
            Schema::table('privacy_policies', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('privacy_policies');
                if (!in_array('privacy_policies_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'privacy_policies_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('privacy_policies_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'privacy_policies_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('projects');
                if (!in_array('fk_projects_goal_funding', $foreignKeys))
                    $table->foreign(['goal_funding_id'], 'fk_projects_goal_funding')->references(['id'])->on('project_goal_fundings_v2')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_projects_goal_involvements', $foreignKeys))
                    $table->foreign(['goal_involvement_id'], 'fk_projects_goal_involvements')->references(['id'])->on('project_goal_involvements_v2')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_projects_image', $foreignKeys))
                    $table->foreign(['image_id'], 'fk_projects_image')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_projects_location', $foreignKeys))
                    $table->foreign(['location_id'], 'fk_projects_location')->references(['id'])->on('locations_v2')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_projects_partner', $foreignKeys))
                    $table->foreign(['partner_id'], 'fk_projects_partner')->references(['id'])->on('partners')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_projects_region', $foreignKeys))
                    $table->foreign(['region_id'], 'fk_projects_region')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_projects_teaser_image', $foreignKeys))
                    $table->foreign(['teaser_image_id'], 'fk_projects_teaser_image')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('projects_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'projects_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('projects_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'projects_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('projects_related_projects_links')) {
            Schema::table('projects_related_projects_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('projects_related_projects_links');
                if (!in_array('projects_related_projects_links_fk', $foreignKeys))
                    $table->foreign(['project_id'], 'projects_related_projects_links_fk')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('projects_related_projects_links_inv_fk', $foreignKeys))
                    $table->foreign(['inv_project_id'], 'projects_related_projects_links_inv_fk')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('projects_users_favorited_links')) {
            Schema::table('projects_users_favorited_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('projects_users_favorited_links');
                if (!in_array('projects_users_favorited_links_fk', $foreignKeys))
                    $table->foreign(['project_id'], 'projects_users_favorited_links_fk')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('projects_users_favorited_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'projects_users_favorited_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('projects_users_joined_links')) {
            Schema::table('projects_users_joined_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('projects_users_joined_links');
                if (!in_array('projects_users_joined_links_fk', $foreignKeys))
                    $table->foreign(['project_id'], 'projects_users_joined_links_fk')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('projects_users_joined_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'projects_users_joined_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('quiz_answers')) {
            Schema::table('quiz_answers', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('quiz_answers');
                if (!in_array('fk_quiz_questions_quiz_answers', $foreignKeys))
                    $table->foreign(['quiz_question_id'], 'fk_quiz_questions_quiz_answers')->references(['id'])->on('quiz_questions')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('quiz_questions')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('quiz_questions');
                if (!in_array('fk_quiz_questions_region', $foreignKeys))
                    $table->foreign(['region_id'], 'fk_quiz_questions_region')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('quiz_responses_v2')) {
            Schema::table('quiz_responses_v2', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('quiz_responses_v2');
                if (!in_array('fk_quiz_responses_quiz_id', $foreignKeys))
                    $table->foreign(['quiz_id'], 'fk_quiz_responses_quiz_id')->references(['id'])->on('quizzes')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_quiz_responses_user_id', $foreignKeys))
                    $table->foreign(['user_id'], 'fk_quiz_responses_user_id')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('quiz_streaks')) {
            Schema::table('quiz_streaks', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('quiz_streaks');
                if (!in_array('quiz_streaks_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'quiz_streaks_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('quiz_streaks_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'quiz_streaks_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('quizzes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('quizzes');
                if (!in_array('fk_quizzes_quiz_questions', $foreignKeys))
                    $table->foreign(['quiz_question_id'], 'fk_quizzes_quiz_questions')->references(['id'])->on('quiz_questions')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('quizzes_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'quizzes_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('quizzes_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'quizzes_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('redeemed-voucher')) {
            Schema::table('redeemed-voucher', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('redeemed-voucher');
                if (!in_array('redeemed-voucher_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'redeemed-voucher_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('redeemed-voucher_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'redeemed-voucher_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('regions')) {
            Schema::table('regions', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('regions');
                if (!in_array('regions_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'regions_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('regions_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'regions_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('role_has_permissions')) {
            Schema::table('role_has_permissions', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('role_has_permissions');
                if (!in_array('role_has_permissions_permission_id_foreign', $foreignKeys))
                    $table->foreign(['permission_id'], 'role_has_permissions_permission_id_foreign')->references(['id'])->on('admin_permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('sponsor_memberships')) {
            Schema::table('sponsor_memberships', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('sponsor_memberships');
                if (!in_array('sponsor_memberships_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'sponsor_memberships_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('sponsor_memberships_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'sponsor_memberships_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('surveys')) {
            Schema::table('surveys', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('surveys');
                if (!in_array('surveys_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'surveys_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('surveys_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'surveys_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('surveys_evaluated_by_links')) {
            Schema::table('surveys_evaluated_by_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('surveys_evaluated_by_links');
                if (!in_array('surveys_evaluated_by_links_fk', $foreignKeys))
                    $table->foreign(['survey_id'], 'surveys_evaluated_by_links_fk')->references(['id'])->on('surveys')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('surveys_evaluated_by_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'surveys_evaluated_by_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('surveys_participants_links')) {
            Schema::table('surveys_participants_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('surveys_participants_links');
                if (!in_array('surveys_participants_links_fk', $foreignKeys))
                    $table->foreign(['survey_id'], 'surveys_participants_links_fk')->references(['id'])->on('surveys')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('surveys_participants_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'surveys_participants_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('trophies')) {
            Schema::table('trophies', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('trophies');
                if (!in_array('fk_trophies_challenge', $foreignKeys))
                    $table->foreign(['challenge_id'], 'fk_trophies_challenge')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_trophies_image', $foreignKeys))
                    $table->foreign(['image_id'], 'fk_trophies_image')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('trophies_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'trophies_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('trophies_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'trophies_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('up_permissions')) {
            Schema::table('up_permissions', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('up_permissions');
                if (!in_array('up_permissions_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'up_permissions_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('up_permissions_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'up_permissions_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('up_permissions_role_links')) {
            Schema::table('up_permissions_role_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('up_permissions_role_links');
                if (!in_array('up_permissions_role_links_fk', $foreignKeys))
                    $table->foreign(['permission_id'], 'up_permissions_role_links_fk')->references(['id'])->on('up_permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('up_permissions_role_links_inv_fk', $foreignKeys))
                    $table->foreign(['role_id'], 'up_permissions_role_links_inv_fk')->references(['id'])->on('up_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('up_roles')) {
            Schema::table('up_roles', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('up_roles');
                if (!in_array('up_roles_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'up_roles_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('up_roles_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'up_roles_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('up_users')) {
            Schema::table('up_users', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('up_users');
                if (!in_array('fk_up_users_avatar', $foreignKeys))
                    $table->foreign(['avatar_id'], 'fk_up_users_avatar')->references(['id'])->on('user_avatars')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_up_users_quiz_streak', $foreignKeys))
                    $table->foreign(['quiz_streak_id'], 'fk_up_users_quiz_streak')->references(['id'])->on('quiz_streaks')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_up_users_region', $foreignKeys))
                    $table->foreign(['region_id'], 'fk_up_users_region')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_up_users_role', $foreignKeys))
                    $table->foreign(['role_id'], 'fk_up_users_role')->references(['id'])->on('up_roles')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('up_users_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'up_users_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('up_users_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'up_users_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('up_users_favorited_news_links')) {
            Schema::table('up_users_favorited_news_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('up_users_favorited_news_links');
                if (!in_array('up_users_favorited_news_links_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'up_users_favorited_news_links_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('up_users_favorited_news_links_inv_fk', $foreignKeys))
                    $table->foreign(['news_entry_id'], 'up_users_favorited_news_links_inv_fk')->references(['id'])->on('news_entries')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('upload_folders')) {
            Schema::table('upload_folders', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('upload_folders');
                if (!in_array('upload_folders_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'upload_folders_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('upload_folders_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'upload_folders_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('upload_folders_parent_links')) {
            Schema::table('upload_folders_parent_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('upload_folders_parent_links');
                if (!in_array('upload_folders_parent_links_fk', $foreignKeys))
                    $table->foreign(['folder_id'], 'upload_folders_parent_links_fk')->references(['id'])->on('upload_folders')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('upload_folders_parent_links_inv_fk', $foreignKeys))
                    $table->foreign(['inv_folder_id'], 'upload_folders_parent_links_inv_fk')->references(['id'])->on('upload_folders')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('user_avatars')) {
            Schema::table('user_avatars', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('user_avatars');
                if (!in_array('fk_user_avatars_image', $foreignKeys))
                    $table->foreign(['image_id'], 'fk_user_avatars_image')->references(['id'])->on('files')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('user_avatars_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'user_avatars_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('user_avatars_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'user_avatars_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('user_follows_v2')) {
            Schema::table('user_follows_v2', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('user_follows_v2');
                if (!in_array('fk_user_follows_followed_user_id', $foreignKeys))
                    $table->foreign(['followed_user_id'], 'fk_user_follows_followed_user_id')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_user_follows_follows_user_id', $foreignKeys))
                    $table->foreign(['follows_user_id'], 'fk_user_follows_follows_user_id')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('user_trophies_v2')) {
            Schema::table('user_trophies_v2', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('user_trophies_v2');
                if (!in_array('fk_trophies_trophy_id', $foreignKeys))
                    $table->foreign(['trophy_id'], 'fk_trophies_trophy_id')->references(['id'])->on('trophies')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_trophies_user_id', $foreignKeys))
                    $table->foreign(['user_id'], 'fk_trophies_user_id')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('voucher_redemptions')) {
            Schema::table('voucher_redemptions', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('voucher_redemptions');
                if (!in_array('fk_voucher_redemptions_offer', $foreignKeys))
                    $table->foreign(['offer_id'], 'fk_voucher_redemptions_offer')->references(['id'])->on('offers')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('fk_voucher_redemptions_redeemer', $foreignKeys))
                    $table->foreign(['redeemer_id'], 'fk_voucher_redemptions_redeemer')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('voucher_redemptions_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'voucher_redemptions_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('voucher_redemptions_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'voucher_redemptions_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('zz_admin_users_roles_links')) {
            Schema::table('zz_admin_users_roles_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_admin_users_roles_links');
                if (!in_array('admin_users_roles_links_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'admin_users_roles_links_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenge_templates_components')) {
            Schema::table('zz_challenge_templates_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenge_templates_components');
                if (!in_array('challenge_templates_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'challenge_templates_entity_fk')->references(['id'])->on('challenge_templates')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenge_templates_partner_links')) {
            Schema::table('zz_challenge_templates_partner_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenge_templates_partner_links');
                if (!in_array('challenge_templates_partner_links_fk', $foreignKeys))
                    $table->foreign(['challenge_template_id'], 'challenge_templates_partner_links_fk')->references(['id'])->on('challenge_templates')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('challenge_templates_partner_links_inv_fk', $foreignKeys))
                    $table->foreign(['partner_id'], 'challenge_templates_partner_links_inv_fk')->references(['id'])->on('partners')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenges_author_links')) {
            Schema::table('zz_challenges_author_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenges_author_links');
                if (!in_array('challenges_author_links_fk', $foreignKeys))
                    $table->foreign(['challenge_id'], 'challenges_author_links_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('challenges_author_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'challenges_author_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenges_components')) {
            Schema::table('zz_challenges_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenges_components');
                if (!in_array('challenges_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'challenges_entity_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenges_image_links')) {
            Schema::table('zz_challenges_image_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenges_image_links');
                if (!in_array('challenges_image_links_fk', $foreignKeys))
                    $table->foreign(['challenge_id'], 'challenges_image_links_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('challenges_image_links_inv_fk', $foreignKeys))
                    $table->foreign(['challenge_image_id'], 'challenges_image_links_inv_fk')->references(['id'])->on('challenge_images')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenges_partner_links')) {
            Schema::table('zz_challenges_partner_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenges_partner_links');
                if (!in_array('challenges_partner_links_fk', $foreignKeys))
                    $table->foreign(['challenge_id'], 'challenges_partner_links_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('challenges_partner_links_inv_fk', $foreignKeys))
                    $table->foreign(['partner_id'], 'challenges_partner_links_inv_fk')->references(['id'])->on('partners')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenges_region_links')) {
            Schema::table('zz_challenges_region_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenges_region_links');
                if (!in_array('challenges_region_links_fk', $foreignKeys))
                    $table->foreign(['challenge_id'], 'challenges_region_links_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('challenges_region_links_inv_fk', $foreignKeys))
                    $table->foreign(['region_id'], 'challenges_region_links_inv_fk')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_challenges_users_joined_links')) {
            Schema::table('zz_challenges_users_joined_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_challenges_users_joined_links');
                if (!in_array('challenges_users_joined_links_fk', $foreignKeys))
                    $table->foreign(['challenge_id'], 'challenges_users_joined_links_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('challenges_users_joined_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'challenges_users_joined_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_components_project_locations_components')) {
            Schema::table('zz_components_project_locations_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_components_project_locations_components');
                if (!in_array('components_project_locations_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'components_project_locations_entity_fk')->references(['id'])->on('locations_v2')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_components_user_trophies_trophy_links')) {
            Schema::table('zz_components_user_trophies_trophy_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_components_user_trophies_trophy_links');
                if (!in_array('components_user_trophies_trophy_links_fk', $foreignKeys))
                    $table->foreign(['trophies_id'], 'components_user_trophies_trophy_links_fk')->references(['id'])->on('zz_components_user_trophies')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('components_user_trophies_trophy_links_inv_fk', $foreignKeys))
                    $table->foreign(['trophy_id'], 'components_user_trophies_trophy_links_inv_fk')->references(['id'])->on('trophies')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_joined_challenges_challenge_links')) {
            Schema::table('zz_joined_challenges_challenge_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_joined_challenges_challenge_links');
                if (!in_array('joined_challenges_challenge_links_fk', $foreignKeys))
                    $table->foreign(['joined_challenge_id'], 'joined_challenges_challenge_links_fk')->references(['id'])->on('joined_challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('joined_challenges_challenge_links_inv_fk', $foreignKeys))
                    $table->foreign(['challenge_id'], 'joined_challenges_challenge_links_inv_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_joined_challenges_components')) {
            Schema::table('zz_joined_challenges_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_joined_challenges_components');
                if (!in_array('joined_challenges_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'joined_challenges_entity_fk')->references(['id'])->on('joined_challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_joined_challenges_user_links')) {
            Schema::table('zz_joined_challenges_user_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_joined_challenges_user_links');
                if (!in_array('joined_challenges_user_links_fk', $foreignKeys))
                    $table->foreign(['joined_challenge_id'], 'joined_challenges_user_links_fk')->references(['id'])->on('joined_challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('joined_challenges_user_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'joined_challenges_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_news_entries_components')) {
            Schema::table('zz_news_entries_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_news_entries_components');
                if (!in_array('news_entries_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'news_entries_entity_fk')->references(['id'])->on('news_entries')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_news_entries_region_links')) {
            Schema::table('zz_news_entries_region_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_news_entries_region_links');
                if (!in_array('news_entries_region_links_fk', $foreignKeys))
                    $table->foreign(['news_entry_id'], 'news_entries_region_links_fk')->references(['id'])->on('news_entries')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('news_entries_region_links_inv_fk', $foreignKeys))
                    $table->foreign(['region_id'], 'news_entries_region_links_inv_fk')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_offers_components')) {
            Schema::table('zz_offers_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_offers_components');
                if (!in_array('offers_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'offers_entity_fk')->references(['id'])->on('offers')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_offers_region_links')) {
            Schema::table('zz_offers_region_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_offers_region_links');
                if (!in_array('offers_region_links_fk', $foreignKeys))
                    $table->foreign(['offer_id'], 'offers_region_links_fk')->references(['id'])->on('offers')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('offers_region_links_inv_fk', $foreignKeys))
                    $table->foreign(['region_id'], 'offers_region_links_inv_fk')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_projects_components')) {
            Schema::table('zz_projects_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_projects_components');
                if (!in_array('projects_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'projects_entity_fk')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_projects_partner_links')) {
            Schema::table('zz_projects_partner_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_projects_partner_links');
                if (!in_array('projects_partner_links_fk', $foreignKeys))
                    $table->foreign(['project_id'], 'projects_partner_links_fk')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('projects_partner_links_inv_fk', $foreignKeys))
                    $table->foreign(['partner_id'], 'projects_partner_links_inv_fk')->references(['id'])->on('partners')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_projects_region_links')) {
            Schema::table('zz_projects_region_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_projects_region_links');
                if (!in_array('projects_region_links_fk', $foreignKeys))
                    $table->foreign(['project_id'], 'projects_region_links_fk')->references(['id'])->on('projects')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('projects_region_links_inv_fk', $foreignKeys))
                    $table->foreign(['region_id'], 'projects_region_links_inv_fk')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_quiz_responses')) {
            Schema::table('zz_quiz_responses', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_quiz_responses');
                if (!in_array('quiz-responses_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'quiz-responses_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('quiz-responses_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'quiz-responses_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('zz_quiz_responses_quiz_links')) {
            Schema::table('zz_quiz_responses_quiz_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_quiz_responses_quiz_links');
                if (!in_array('quiz_responses_quiz_links_fk', $foreignKeys))
                    $table->foreign(['quiz_response_id'], 'quiz_responses_quiz_links_fk')->references(['id'])->on('zz_quiz_responses')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('quiz_responses_quiz_links_inv_fk', $foreignKeys))
                    $table->foreign(['quiz_id'], 'quiz_responses_quiz_links_inv_fk')->references(['id'])->on('quizzes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_quiz_responses_user_links')) {
            Schema::table('zz_quiz_responses_user_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_quiz_responses_user_links');
                if (!in_array('quiz_responses_user_links_fk', $foreignKeys))
                    $table->foreign(['quiz_response_id'], 'quiz_responses_user_links_fk')->references(['id'])->on('zz_quiz_responses')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('quiz_responses_user_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'quiz_responses_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_quiz_streaks_user_links')) {
            Schema::table('zz_quiz_streaks_user_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_quiz_streaks_user_links');
                if (!in_array('quiz_streaks_user_links_fk', $foreignKeys))
                    $table->foreign(['quiz_streak_id'], 'quiz_streaks_user_links_fk')->references(['id'])->on('quiz_streaks')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('quiz_streaks_user_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'quiz_streaks_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_quizzes_components')) {
            Schema::table('zz_quizzes_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_quizzes_components');
                if (!in_array('quizzes_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'quizzes_entity_fk')->references(['id'])->on('quizzes')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_surveys_components')) {
            Schema::table('zz_surveys_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_surveys_components');
                if (!in_array('surveys_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'surveys_entity_fk')->references(['id'])->on('surveys')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_trophies_challenge_links')) {
            Schema::table('zz_trophies_challenge_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_trophies_challenge_links');
                if (!in_array('trophies_challenge_links_fk', $foreignKeys))
                    $table->foreign(['trophy_id'], 'trophies_challenge_links_fk')->references(['id'])->on('trophies')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('trophies_challenge_links_inv_fk', $foreignKeys))
                    $table->foreign(['challenge_id'], 'trophies_challenge_links_inv_fk')->references(['id'])->on('challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_up_users_avatar_links')) {
            Schema::table('zz_up_users_avatar_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_up_users_avatar_links');
                if (!in_array('up_users_avatar_links_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'up_users_avatar_links_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('up_users_avatar_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_avatar_id'], 'up_users_avatar_links_inv_fk')->references(['id'])->on('user_avatars')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_up_users_components')) {
            Schema::table('zz_up_users_components', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_up_users_components');
                if (!in_array('up_users_entity_fk', $foreignKeys))
                    $table->foreign(['entity_id'], 'up_users_entity_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_up_users_joined_challenges_links')) {
            Schema::table('zz_up_users_joined_challenges_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_up_users_joined_challenges_links');
                if (!in_array('up_users_joined_challenges_links_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'up_users_joined_challenges_links_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('up_users_joined_challenges_links_inv_fk', $foreignKeys))
                    $table->foreign(['joined_challenge_id'], 'up_users_joined_challenges_links_inv_fk')->references(['id'])->on('joined_challenges')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_up_users_region_links')) {
            Schema::table('zz_up_users_region_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_up_users_region_links');
                if (!in_array('up_users_region_links_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'up_users_region_links_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('up_users_region_links_inv_fk', $foreignKeys))
                    $table->foreign(['region_id'], 'up_users_region_links_inv_fk')->references(['id'])->on('regions')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_up_users_role_links')) {
            Schema::table('zz_up_users_role_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_up_users_role_links');
                if (!in_array('up_users_role_links_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'up_users_role_links_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('up_users_role_links_inv_fk', $foreignKeys))
                    $table->foreign(['role_id'], 'up_users_role_links_inv_fk')->references(['id'])->on('up_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_user_follows')) {
            Schema::table('zz_user_follows', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_user_follows');
                if (!in_array('user_follows_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'user_follows_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('user_follows_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'user_follows_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('zz_user_follows_followed_user_links')) {
            Schema::table('zz_user_follows_followed_user_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_user_follows_followed_user_links');
                if (!in_array('user_follows_followed_user_links_fk', $foreignKeys))
                    $table->foreign(['user_follow_id'], 'user_follows_followed_user_links_fk')->references(['id'])->on('zz_user_follows')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('user_follows_followed_user_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'user_follows_followed_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_user_follows_user_links')) {
            Schema::table('zz_user_follows_user_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_user_follows_user_links');
                if (!in_array('user_follows_user_links_fk', $foreignKeys))
                    $table->foreign(['user_follow_id'], 'user_follows_user_links_fk')->references(['id'])->on('zz_user_follows')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('user_follows_user_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'user_follows_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_user_trophies')) {
            Schema::table('zz_user_trophies', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_user_trophies');
                if (!in_array('user_trophies_created_by_id_fk', $foreignKeys))
                    $table->foreign(['created_by_id'], 'user_trophies_created_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                if (!in_array('user_trophies_updated_by_id_fk', $foreignKeys))
                    $table->foreign(['updated_by_id'], 'user_trophies_updated_by_id_fk')->references(['id'])->on('admin_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            });
        }

        if (Schema::hasTable('zz_user_trophies_trophy_links')) {
            Schema::table('zz_user_trophies_trophy_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_user_trophies_trophy_links');
                if (!in_array('user_trophies_trophy_links_fk', $foreignKeys))
                    $table->foreign(['user_trophy_id'], 'user_trophies_trophy_links_fk')->references(['id'])->on('zz_user_trophies')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('user_trophies_trophy_links_inv_fk', $foreignKeys))
                    $table->foreign(['trophy_id'], 'user_trophies_trophy_links_inv_fk')->references(['id'])->on('trophies')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_user_trophies_user_links')) {
            Schema::table('zz_user_trophies_user_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_user_trophies_user_links');
                if (!in_array('user_trophies_user_links_fk', $foreignKeys))
                    $table->foreign(['user_trophy_id'], 'user_trophies_user_links_fk')->references(['id'])->on('zz_user_trophies')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('user_trophies_user_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'user_trophies_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_voucher_redemptions_offer_links')) {
            Schema::table('zz_voucher_redemptions_offer_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_voucher_redemptions_offer_links');
                if (!in_array('voucher_redemptions_offer_links_fk', $foreignKeys))
                    $table->foreign(['voucher_redemption_id'], 'voucher_redemptions_offer_links_fk')->references(['id'])->on('voucher_redemptions')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('voucher_redemptions_offer_links_inv_fk', $foreignKeys))
                    $table->foreign(['offer_id'], 'voucher_redemptions_offer_links_inv_fk')->references(['id'])->on('offers')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }

        if (Schema::hasTable('zz_voucher_redemptions_user_links')) {
            Schema::table('zz_voucher_redemptions_user_links', function (Blueprint $table) {
                $foreignKeys = $this->listTableForeignKeys('zz_voucher_redemptions_user_links');
                if (!in_array('voucher_redemptions_user_links_fk', $foreignKeys))
                    $table->foreign(['voucher_redemption_id'], 'voucher_redemptions_user_links_fk')->references(['id'])->on('voucher_redemptions')->onUpdate('NO ACTION')->onDelete('CASCADE');
                if (!in_array('voucher_redemptions_user_links_inv_fk', $foreignKeys))
                    $table->foreign(['user_id'], 'voucher_redemptions_user_links_inv_fk')->references(['id'])->on('up_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('zz_voucher_redemptions_user_links')) {
            Schema::table('zz_voucher_redemptions_user_links', function (Blueprint $table) {
                $table->dropForeign('voucher_redemptions_user_links_fk');
                $table->dropForeign('voucher_redemptions_user_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_voucher_redemptions_offer_links')) {
            Schema::table('zz_voucher_redemptions_offer_links', function (Blueprint $table) {
                $table->dropForeign('voucher_redemptions_offer_links_fk');
                $table->dropForeign('voucher_redemptions_offer_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_user_trophies_user_links')) {
            Schema::table('zz_user_trophies_user_links', function (Blueprint $table) {
                $table->dropForeign('user_trophies_user_links_fk');
                $table->dropForeign('user_trophies_user_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_user_trophies_trophy_links')) {
            Schema::table('zz_user_trophies_trophy_links', function (Blueprint $table) {
                $table->dropForeign('user_trophies_trophy_links_fk');
                $table->dropForeign('user_trophies_trophy_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_user_trophies')) {
            Schema::table('zz_user_trophies', function (Blueprint $table) {
                $table->dropForeign('user_trophies_created_by_id_fk');
                $table->dropForeign('user_trophies_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('zz_user_follows_user_links')) {
            Schema::table('zz_user_follows_user_links', function (Blueprint $table) {
                $table->dropForeign('user_follows_user_links_fk');
                $table->dropForeign('user_follows_user_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_user_follows_followed_user_links')) {
            Schema::table('zz_user_follows_followed_user_links', function (Blueprint $table) {
                $table->dropForeign('user_follows_followed_user_links_fk');
                $table->dropForeign('user_follows_followed_user_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_user_follows')) {
            Schema::table('zz_user_follows', function (Blueprint $table) {
                $table->dropForeign('user_follows_created_by_id_fk');
                $table->dropForeign('user_follows_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('zz_up_users_role_links')) {
            Schema::table('zz_up_users_role_links', function (Blueprint $table) {
                $table->dropForeign('up_users_role_links_fk');
                $table->dropForeign('up_users_role_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_up_users_region_links')) {
            Schema::table('zz_up_users_region_links', function (Blueprint $table) {
                $table->dropForeign('up_users_region_links_fk');
                $table->dropForeign('up_users_region_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_up_users_joined_challenges_links')) {
            Schema::table('zz_up_users_joined_challenges_links', function (Blueprint $table) {
                $table->dropForeign('up_users_joined_challenges_links_fk');
                $table->dropForeign('up_users_joined_challenges_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_up_users_components')) {
            Schema::table('zz_up_users_components', function (Blueprint $table) {
                $table->dropForeign('up_users_entity_fk');
            });
        }

        if (Schema::hasTable('zz_up_users_avatar_links')) {
            Schema::table('zz_up_users_avatar_links', function (Blueprint $table) {
                $table->dropForeign('up_users_avatar_links_fk');
                $table->dropForeign('up_users_avatar_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_trophies_challenge_links')) {
            Schema::table('zz_trophies_challenge_links', function (Blueprint $table) {
                $table->dropForeign('trophies_challenge_links_fk');
                $table->dropForeign('trophies_challenge_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_surveys_components')) {
            Schema::table('zz_surveys_components', function (Blueprint $table) {
                $table->dropForeign('surveys_entity_fk');
            });
        }

        if (Schema::hasTable('zz_quizzes_components')) {
            Schema::table('zz_quizzes_components', function (Blueprint $table) {
                $table->dropForeign('quizzes_entity_fk');
            });
        }

        if (Schema::hasTable('zz_quiz_streaks_user_links')) {
            Schema::table('zz_quiz_streaks_user_links', function (Blueprint $table) {
                $table->dropForeign('quiz_streaks_user_links_fk');
                $table->dropForeign('quiz_streaks_user_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_quiz_responses_user_links')) {
            Schema::table('zz_quiz_responses_user_links', function (Blueprint $table) {
                $table->dropForeign('quiz_responses_user_links_fk');
                $table->dropForeign('quiz_responses_user_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_quiz_responses_quiz_links')) {
            Schema::table('zz_quiz_responses_quiz_links', function (Blueprint $table) {
                $table->dropForeign('quiz_responses_quiz_links_fk');
                $table->dropForeign('quiz_responses_quiz_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_quiz_responses')) {
            Schema::table('zz_quiz_responses', function (Blueprint $table) {
                $table->dropForeign('quiz-responses_created_by_id_fk');
                $table->dropForeign('quiz-responses_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('zz_projects_region_links')) {
            Schema::table('zz_projects_region_links', function (Blueprint $table) {
                $table->dropForeign('projects_region_links_fk');
                $table->dropForeign('projects_region_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_projects_partner_links')) {
            Schema::table('zz_projects_partner_links', function (Blueprint $table) {
                $table->dropForeign('projects_partner_links_fk');
                $table->dropForeign('projects_partner_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_projects_components')) {
            Schema::table('zz_projects_components', function (Blueprint $table) {
                $table->dropForeign('projects_entity_fk');
            });
        }

        if (Schema::hasTable('zz_offers_region_links')) {
            Schema::table('zz_offers_region_links', function (Blueprint $table) {
                $table->dropForeign('offers_region_links_fk');
                $table->dropForeign('offers_region_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_offers_components')) {
            Schema::table('zz_offers_components', function (Blueprint $table) {
                $table->dropForeign('offers_entity_fk');
            });
        }

        if (Schema::hasTable('zz_news_entries_region_links')) {
            Schema::table('zz_news_entries_region_links', function (Blueprint $table) {
                $table->dropForeign('news_entries_region_links_fk');
                $table->dropForeign('news_entries_region_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_news_entries_components')) {
            Schema::table('zz_news_entries_components', function (Blueprint $table) {
                $table->dropForeign('news_entries_entity_fk');
            });
        }

        if (Schema::hasTable('zz_joined_challenges_user_links')) {
            Schema::table('zz_joined_challenges_user_links', function (Blueprint $table) {
                $table->dropForeign('joined_challenges_user_links_fk');
                $table->dropForeign('joined_challenges_user_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_joined_challenges_components')) {
            Schema::table('zz_joined_challenges_components', function (Blueprint $table) {
                $table->dropForeign('joined_challenges_entity_fk');
            });
        }

        if (Schema::hasTable('zz_joined_challenges_challenge_links')) {
            Schema::table('zz_joined_challenges_challenge_links', function (Blueprint $table) {
                $table->dropForeign('joined_challenges_challenge_links_fk');
                $table->dropForeign('joined_challenges_challenge_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_components_user_trophies_trophy_links')) {
            Schema::table('zz_components_user_trophies_trophy_links', function (Blueprint $table) {
                $table->dropForeign('components_user_trophies_trophy_links_fk');
                $table->dropForeign('components_user_trophies_trophy_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_components_project_locations_components')) {
            Schema::table('zz_components_project_locations_components', function (Blueprint $table) {
                $table->dropForeign('components_project_locations_entity_fk');
            });
        }

        if (Schema::hasTable('zz_challenges_users_joined_links')) {
            Schema::table('zz_challenges_users_joined_links', function (Blueprint $table) {
                $table->dropForeign('challenges_users_joined_links_fk');
                $table->dropForeign('challenges_users_joined_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_challenges_region_links')) {
            Schema::table('zz_challenges_region_links', function (Blueprint $table) {
                $table->dropForeign('challenges_region_links_fk');
                $table->dropForeign('challenges_region_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_challenges_partner_links')) {
            Schema::table('zz_challenges_partner_links', function (Blueprint $table) {
                $table->dropForeign('challenges_partner_links_fk');
                $table->dropForeign('challenges_partner_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_challenges_image_links')) {
            Schema::table('zz_challenges_image_links', function (Blueprint $table) {
                $table->dropForeign('challenges_image_links_fk');
                $table->dropForeign('challenges_image_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_challenges_components')) {
            Schema::table('zz_challenges_components', function (Blueprint $table) {
                $table->dropForeign('challenges_entity_fk');
            });
        }

        if (Schema::hasTable('zz_challenges_author_links')) {
            Schema::table('zz_challenges_author_links', function (Blueprint $table) {
                $table->dropForeign('challenges_author_links_fk');
                $table->dropForeign('challenges_author_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_challenge_templates_partner_links')) {
            Schema::table('zz_challenge_templates_partner_links', function (Blueprint $table) {
                $table->dropForeign('challenge_templates_partner_links_fk');
                $table->dropForeign('challenge_templates_partner_links_inv_fk');
            });
        }

        if (Schema::hasTable('zz_challenge_templates_components')) {
            Schema::table('zz_challenge_templates_components', function (Blueprint $table) {
                $table->dropForeign('challenge_templates_entity_fk');
            });
        }

        if (Schema::hasTable('zz_admin_users_roles_links')) {
            Schema::table('zz_admin_users_roles_links', function (Blueprint $table) {
                $table->dropForeign('admin_users_roles_links_fk');
            });
        }

        if (Schema::hasTable('voucher_redemptions')) {
            Schema::table('voucher_redemptions', function (Blueprint $table) {
                $table->dropForeign('fk_voucher_redemptions_offer');
                $table->dropForeign('fk_voucher_redemptions_redeemer');
                $table->dropForeign('voucher_redemptions_created_by_id_fk');
                $table->dropForeign('voucher_redemptions_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('user_trophies_v2')) {
            Schema::table('user_trophies_v2', function (Blueprint $table) {
                $table->dropForeign('fk_trophies_trophy_id');
                $table->dropForeign('fk_trophies_user_id');
            });
        }

        if (Schema::hasTable('user_follows_v2')) {
            Schema::table('user_follows_v2', function (Blueprint $table) {
                $table->dropForeign('fk_user_follows_followed_user_id');
                $table->dropForeign('fk_user_follows_follows_user_id');
            });
        }

        if (Schema::hasTable('user_avatars')) {
            Schema::table('user_avatars', function (Blueprint $table) {
                $table->dropForeign('fk_user_avatars_image');
                $table->dropForeign('user_avatars_created_by_id_fk');
                $table->dropForeign('user_avatars_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('upload_folders_parent_links')) {
            Schema::table('upload_folders_parent_links', function (Blueprint $table) {
                $table->dropForeign('upload_folders_parent_links_fk');
                $table->dropForeign('upload_folders_parent_links_inv_fk');
            });
        }

        if (Schema::hasTable('upload_folders')) {
            Schema::table('upload_folders', function (Blueprint $table) {
                $table->dropForeign('upload_folders_created_by_id_fk');
                $table->dropForeign('upload_folders_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('up_users_favorited_news_links')) {
            Schema::table('up_users_favorited_news_links', function (Blueprint $table) {
                $table->dropForeign('up_users_favorited_news_links_fk');
                $table->dropForeign('up_users_favorited_news_links_inv_fk');
            });
        }

        if (Schema::hasTable('up_users')) {
            Schema::table('up_users', function (Blueprint $table) {
                $table->dropForeign('fk_up_users_avatar');
                $table->dropForeign('fk_up_users_quiz_streak');
                $table->dropForeign('fk_up_users_region');
                $table->dropForeign('fk_up_users_role');
                $table->dropForeign('up_users_created_by_id_fk');
                $table->dropForeign('up_users_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('up_roles')) {
            Schema::table('up_roles', function (Blueprint $table) {
                $table->dropForeign('up_roles_created_by_id_fk');
                $table->dropForeign('up_roles_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('up_permissions_role_links')) {
            Schema::table('up_permissions_role_links', function (Blueprint $table) {
                $table->dropForeign('up_permissions_role_links_fk');
                $table->dropForeign('up_permissions_role_links_inv_fk');
            });
        }

        if (Schema::hasTable('up_permissions')) {
            Schema::table('up_permissions', function (Blueprint $table) {
                $table->dropForeign('up_permissions_created_by_id_fk');
                $table->dropForeign('up_permissions_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('trophies')) {
            Schema::table('trophies', function (Blueprint $table) {
                $table->dropForeign('fk_trophies_challenge');
                $table->dropForeign('fk_trophies_image');
                $table->dropForeign('trophies_created_by_id_fk');
                $table->dropForeign('trophies_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('surveys_participants_links')) {
            Schema::table('surveys_participants_links', function (Blueprint $table) {
                $table->dropForeign('surveys_participants_links_fk');
                $table->dropForeign('surveys_participants_links_inv_fk');
            });
        }

        if (Schema::hasTable('surveys_evaluated_by_links')) {
            Schema::table('surveys_evaluated_by_links', function (Blueprint $table) {
                $table->dropForeign('surveys_evaluated_by_links_fk');
                $table->dropForeign('surveys_evaluated_by_links_inv_fk');
            });
        }

        if (Schema::hasTable('surveys')) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->dropForeign('surveys_created_by_id_fk');
                $table->dropForeign('surveys_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('sponsor_memberships')) {
            Schema::table('sponsor_memberships', function (Blueprint $table) {
                $table->dropForeign('sponsor_memberships_created_by_id_fk');
                $table->dropForeign('sponsor_memberships_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('role_has_permissions')) {
            Schema::table('role_has_permissions', function (Blueprint $table) {
                $table->dropForeign('role_has_permissions_permission_id_foreign');
                $table->dropForeign('role_has_permissions_role_id_foreign');
            });
        }

        if (Schema::hasTable('regions')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->dropForeign('regions_created_by_id_fk');
                $table->dropForeign('regions_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('redeemed-voucher')) {
            Schema::table('redeemed-voucher', function (Blueprint $table) {
                $table->dropForeign('redeemed-voucher_created_by_id_fk');
                $table->dropForeign('redeemed-voucher_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('quizzes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropForeign('fk_quizzes_quiz_questions');
                $table->dropForeign('quizzes_created_by_id_fk');
                $table->dropForeign('quizzes_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('quiz_streaks')) {
            Schema::table('quiz_streaks', function (Blueprint $table) {
                $table->dropForeign('quiz_streaks_created_by_id_fk');
                $table->dropForeign('quiz_streaks_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('quiz_responses_v2')) {
            Schema::table('quiz_responses_v2', function (Blueprint $table) {
                $table->dropForeign('fk_quiz_responses_quiz_id');
                $table->dropForeign('fk_quiz_responses_user_id');
            });
        }

        if (Schema::hasTable('quiz_questions')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->dropForeign('fk_quiz_questions_region');
            });
        }

        if (Schema::hasTable('quiz_answers')) {
            Schema::table('quiz_answers', function (Blueprint $table) {
                $table->dropForeign('fk_quiz_questions_quiz_answers');
            });
        }

        if (Schema::hasTable('projects_users_joined_links')) {
            Schema::table('projects_users_joined_links', function (Blueprint $table) {
                $table->dropForeign('projects_users_joined_links_fk');
                $table->dropForeign('projects_users_joined_links_inv_fk');
            });
        }

        if (Schema::hasTable('projects_users_favorited_links')) {
            Schema::table('projects_users_favorited_links', function (Blueprint $table) {
                $table->dropForeign('projects_users_favorited_links_fk');
                $table->dropForeign('projects_users_favorited_links_inv_fk');
            });
        }

        if (Schema::hasTable('projects_related_projects_links')) {
            Schema::table('projects_related_projects_links', function (Blueprint $table) {
                $table->dropForeign('projects_related_projects_links_fk');
                $table->dropForeign('projects_related_projects_links_inv_fk');
            });
        }

        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeign('fk_projects_goal_funding');
                $table->dropForeign('fk_projects_goal_involvements');
                $table->dropForeign('fk_projects_image');
                $table->dropForeign('fk_projects_location');
                $table->dropForeign('fk_projects_partner');
                $table->dropForeign('fk_projects_region');
                $table->dropForeign('fk_projects_teaser_image');
                $table->dropForeign('projects_created_by_id_fk');
                $table->dropForeign('projects_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('privacy_policies')) {
            Schema::table('privacy_policies', function (Blueprint $table) {
                $table->dropForeign('privacy_policies_created_by_id_fk');
                $table->dropForeign('privacy_policies_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('partnerships')) {
            Schema::table('partnerships', function (Blueprint $table) {
                $table->dropForeign('partnerships_created_by_id_fk');
                $table->dropForeign('partnerships_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('partners')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropForeign('fk_partners_logo');
                $table->dropForeign('partners_created_by_id_fk');
                $table->dropForeign('partners_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('partner_inquiries')) {
            Schema::table('partner_inquiries', function (Blueprint $table) {
                $table->dropForeign('partner_inquiries_created_by_id_fk');
                $table->dropForeign('partner_inquiries_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('offers')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->dropForeign('fk_offers_address');
                $table->dropForeign('fk_offers_image');
                $table->dropForeign('fk_offers_location');
                $table->dropForeign('fk_offers_region');
                $table->dropForeign('offers_created_by_id_fk');
                $table->dropForeign('offers_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('news_tags')) {
            Schema::table('news_tags', function (Blueprint $table) {
                $table->dropForeign('news_tags_created_by_id_fk');
                $table->dropForeign('news_tags_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('news_entries_users_read_links')) {
            Schema::table('news_entries_users_read_links', function (Blueprint $table) {
                $table->dropForeign('news_entries_users_read_links_fk');
                $table->dropForeign('news_entries_users_read_links_inv_fk');
            });
        }

        if (Schema::hasTable('news_entries_tags_links')) {
            Schema::table('news_entries_tags_links', function (Blueprint $table) {
                $table->dropForeign('news_entries_tags_links_fk');
                $table->dropForeign('news_entries_tags_links_inv_fk');
            });
        }

        if (Schema::hasTable('news_entries')) {
            Schema::table('news_entries', function (Blueprint $table) {
                $table->dropForeign('fk_news_entries_image');
                $table->dropForeign('fk_news_entries_region');
                $table->dropForeign('news_entries_created_by_id_fk');
                $table->dropForeign('news_entries_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('legal_notices')) {
            Schema::table('legal_notices', function (Blueprint $table) {
                $table->dropForeign('legal_notices_created_by_id_fk');
                $table->dropForeign('legal_notices_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('joined_challenges_answers')) {
            Schema::table('joined_challenges_answers', function (Blueprint $table) {
                $table->dropForeign('fk_joined_challenges_answers_challenge_id');
            });
        }

        if (Schema::hasTable('joined_challenges')) {
            Schema::table('joined_challenges', function (Blueprint $table) {
                $table->dropForeign('fk_joined_challenges_challenge_id');
                $table->dropForeign('fk_joined_challenges_user_id');
                $table->dropForeign('joined_challenges_created_by_id_fk');
                $table->dropForeign('joined_challenges_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('i18n_locale')) {
            Schema::table('i18n_locale', function (Blueprint $table) {
                $table->dropForeign('i18n_locale_created_by_id_fk');
                $table->dropForeign('i18n_locale_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('files_related_morphs')) {
            Schema::table('files_related_morphs', function (Blueprint $table) {
                $table->dropForeign('files_related_morphs_fk');
            });
        }

        if (Schema::hasTable('files_folder_links')) {
            Schema::table('files_folder_links', function (Blueprint $table) {
                $table->dropForeign('files_folder_links_fk');
                $table->dropForeign('files_folder_links_inv_fk');
            });
        }

        if (Schema::hasTable('files')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropForeign('files_created_by_id_fk');
                $table->dropForeign('files_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('dashboard')) {
            Schema::table('dashboard', function (Blueprint $table) {
                $table->dropForeign('dashboard_created_by_id_fk');
                $table->dropForeign('dashboard_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('contact_inquiries')) {
            Schema::table('contact_inquiries', function (Blueprint $table) {
                $table->dropForeign('contact_inquiries_created_by_id_fk');
                $table->dropForeign('contact_inquiries_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('challenges_goal_type_steps')) {
            Schema::table('challenges_goal_type_steps', function (Blueprint $table) {
                $table->dropForeign('fk_challenges_goal_type_steps_challenge');
                $table->dropForeign('fk_challenges_goal_type_steps_challenge_template');
            });
        }

        if (Schema::hasTable('challenges_goal_type_measurements')) {
            Schema::table('challenges_goal_type_measurements', function (Blueprint $table) {
                $table->dropForeign('fk_challenges_goal_type_measurements');
                $table->dropForeign('fk_challenges_goal_type_measurements_challenge_template');
            });
        }

        if (Schema::hasTable('challenges')) {
            Schema::table('challenges', function (Blueprint $table) {
                $table->dropForeign('challenges_created_by_id_fk');
                $table->dropForeign('challenges_updated_by_id_fk');
                $table->dropForeign('fk_challenges_author');
                $table->dropForeign('fk_challenges_partner');
                $table->dropForeign('fk_challenges_region');
            });
        }

        if (Schema::hasTable('challenge_templates_image_links')) {
            Schema::table('challenge_templates_image_links', function (Blueprint $table) {
                $table->dropForeign('challenge_templates_image_links_fk');
                $table->dropForeign('challenge_templates_image_links_inv_fk');
            });
        }

        if (Schema::hasTable('challenge_templates')) {
            Schema::table('challenge_templates', function (Blueprint $table) {
                $table->dropForeign('challenge_templates_created_by_id_fk');
                $table->dropForeign('challenge_templates_updated_by_id_fk');
                $table->dropForeign('fk_challenge_templates_partner');
            });
        }

        if (Schema::hasTable('challenge_images')) {
            Schema::table('challenge_images', function (Blueprint $table) {
                $table->dropForeign('challenge_images_created_by_id_fk');
                $table->dropForeign('challenge_images_updated_by_id_fk');
                $table->dropForeign('fk_challenges_images_image');
            });
        }

        if (Schema::hasTable('admin_users')) {
            Schema::table('admin_users', function (Blueprint $table) {
                $table->dropForeign('admin_users_created_by_id_fk');
                $table->dropForeign('admin_users_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('admin_has_roles')) {
            Schema::table('admin_has_roles', function (Blueprint $table) {
                $table->dropForeign('admin_has_roles_role_id_foreign');
            });
        }

        if (Schema::hasTable('admin_has_permissions')) {
            Schema::table('admin_has_permissions', function (Blueprint $table) {
                $table->dropForeign('admin_has_permissions_permission_id_foreign');
            });
        }

        if (Schema::hasTable('actions')) {
            Schema::table('actions', function (Blueprint $table) {
                $table->dropForeign('actions_created_by_id_fk');
                $table->dropForeign('actions_updated_by_id_fk');
            });
        }

        if (Schema::hasTable('abouts')) {
            Schema::table('abouts', function (Blueprint $table) {
                $table->dropForeign('abouts_created_by_id_fk');
                $table->dropForeign('abouts_updated_by_id_fk');
            });
        }

        Schema::dropIfExists('zz_voucher_redemptions_user_links');

        Schema::dropIfExists('zz_voucher_redemptions_offer_links');

        Schema::dropIfExists('zz_user_trophies_user_links');

        Schema::dropIfExists('zz_user_trophies_trophy_links');

        Schema::dropIfExists('zz_user_trophies');

        Schema::dropIfExists('zz_user_follows_user_links');

        Schema::dropIfExists('zz_user_follows_followed_user_links');

        Schema::dropIfExists('zz_user_follows');

        Schema::dropIfExists('zz_up_users_role_links');

        Schema::dropIfExists('zz_up_users_region_links');

        Schema::dropIfExists('zz_up_users_joined_challenges_links');

        Schema::dropIfExists('zz_up_users_components');

        Schema::dropIfExists('zz_up_users_avatar_links');

        Schema::dropIfExists('zz_trophies_challenge_links');

        Schema::dropIfExists('zz_surveys_components');

        Schema::dropIfExists('zz_quizzes_components');

        Schema::dropIfExists('zz_quiz_streaks_user_links');

        Schema::dropIfExists('zz_quiz_responses_user_links');

        Schema::dropIfExists('zz_quiz_responses_quiz_links');

        Schema::dropIfExists('zz_quiz_responses');

        Schema::dropIfExists('zz_projects_region_links');

        Schema::dropIfExists('zz_projects_partner_links');

        Schema::dropIfExists('zz_projects_components');

        Schema::dropIfExists('zz_offers_region_links');

        Schema::dropIfExists('zz_offers_components');

        Schema::dropIfExists('zz_news_entries_region_links');

        Schema::dropIfExists('zz_news_entries_components');

        Schema::dropIfExists('zz_joined_challenges_user_links');

        Schema::dropIfExists('zz_joined_challenges_components');

        Schema::dropIfExists('zz_joined_challenges_challenge_links');

        Schema::dropIfExists('zz_components_user_trophies_trophy_links');

        Schema::dropIfExists('zz_components_user_trophies');

        Schema::dropIfExists('zz_components_project_locations_components');

        Schema::dropIfExists('zz_components_project_coordinates');

        Schema::dropIfExists('zz_components_misc_anonymous_user_ids');

        Schema::dropIfExists('zz_challenges_users_joined_links');

        Schema::dropIfExists('zz_challenges_region_links');

        Schema::dropIfExists('zz_challenges_partner_links');

        Schema::dropIfExists('zz_challenges_image_links');

        Schema::dropIfExists('zz_challenges_components');

        Schema::dropIfExists('zz_challenges_author_links');

        Schema::dropIfExists('zz_challenge_templates_partner_links');

        Schema::dropIfExists('zz_challenge_templates_components');

        Schema::dropIfExists('zz_admin_users_roles_links');

        Schema::dropIfExists('zz_admin_permissions_role_links');

        Schema::dropIfExists('voucher_redemptions');

        Schema::dropIfExists('user_trophies_v2');

        Schema::dropIfExists('user_follows_v2');

        Schema::dropIfExists('user_avatars');

        Schema::dropIfExists('upload_folders_parent_links');

        Schema::dropIfExists('upload_folders');

        Schema::dropIfExists('up_users_favorited_news_links');

        Schema::dropIfExists('up_users');

        Schema::dropIfExists('up_roles');

        Schema::dropIfExists('up_permissions_role_links');

        Schema::dropIfExists('up_permissions');

        Schema::dropIfExists('trophies');

        Schema::dropIfExists('surveys_participants_links');

        Schema::dropIfExists('surveys_evaluated_by_links');

        Schema::dropIfExists('surveys');

        Schema::dropIfExists('sponsor_memberships');

        Schema::dropIfExists('role_has_permissions');

        Schema::dropIfExists('regions');

        Schema::dropIfExists('redeemed-voucher');

        Schema::dropIfExists('quizzes');

        Schema::dropIfExists('quiz_streaks');

        Schema::dropIfExists('quiz_responses_v2');

        Schema::dropIfExists('quiz_questions');

        Schema::dropIfExists('quiz_answers');

        Schema::dropIfExists('projects_users_joined_links');

        Schema::dropIfExists('projects_users_favorited_links');

        Schema::dropIfExists('projects_related_projects_links');

        Schema::dropIfExists('projects');

        Schema::dropIfExists('project_goal_involvements_v2');

        Schema::dropIfExists('project_goal_fundings_v2');

        Schema::dropIfExists('privacy_policies');

        Schema::dropIfExists('personal_access_tokens');

        Schema::dropIfExists('partnerships');

        Schema::dropIfExists('partners');

        Schema::dropIfExists('partner_inquiries');

        Schema::dropIfExists('offers');

        Schema::dropIfExists('news_tags');

        Schema::dropIfExists('news_entries_users_read_links');

        Schema::dropIfExists('news_entries_tags_links');

        Schema::dropIfExists('news_entries');

        Schema::dropIfExists('locations_v2');

        Schema::dropIfExists('legal_notices');

        Schema::dropIfExists('joined_challenges_answers');

        Schema::dropIfExists('joined_challenges');

        Schema::dropIfExists('i18n_locale');

        Schema::dropIfExists('files_related_morphs');

        Schema::dropIfExists('files_folder_links');

        Schema::dropIfExists('files');

        Schema::dropIfExists('dashboard');

        Schema::dropIfExists('contact_inquiries');

        Schema::dropIfExists('challenges_goal_type_steps');

        Schema::dropIfExists('challenges_goal_type_measurements');

        Schema::dropIfExists('challenges');

        Schema::dropIfExists('challenge_templates_image_links');

        Schema::dropIfExists('challenge_templates');

        Schema::dropIfExists('challenge_images');

        Schema::dropIfExists('admin_users');

        Schema::dropIfExists('admin_roles');

        Schema::dropIfExists('admin_permissions');

        Schema::dropIfExists('admin_has_roles');

        Schema::dropIfExists('admin_has_permissions');

        Schema::dropIfExists('addresses_v2');

        Schema::dropIfExists('actions');

        Schema::dropIfExists('abouts');
    }
};
