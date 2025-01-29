TRUNCATE TABLE account,
activity,
activity_amount_modifiers,
activity_cat_charge,
activity_charge,
activity_type,
activity_type_calculation_units,
amount_modifier,
branch,
branch_available_payment_methods,
branch_default_payment_methods,
calculation_unit,
category,
category_supported_classes,
charge_definition,
charge_type,
contact_person,
contact_person_categories2,
country,
custom_field_configuration,
custom_fields,
customer,
customer_accountency_contact_persons,
customer_additional_persons_responsible,
customer_categories,
customer_charge,
customer_customer_persons,
customer_feedback_answer,
customer_feedback_question,
customer_industries,
customer_invoice,
customer_invoice_accountency_persons,
customer_invoice_categories,
customer_invoice_item,
customer_language_combination,
customer_language_combination_specializations,
customer_language_specializations,
customer_languages,
customer_minimal_charge,
customer_payment,
customer_payment_item,
customer_person,
customer_price_list,
customer_price_profile,
customer_services,
customer_price_list_rate,
customer_price_list_language_combination,
customer_rate,
external_system_project,
feedback,
feedback_related_providers,
feedback_related_tasks,
feedback_related_users,
feedback_responsible_for_implementation,
file_stats,
files_for_task_review,
history_entry,
industry,
language_specialization,
lead_source,
opportunity,
opportunity_offer,
opportunity_status,
payment_conditions,
person_department,
person_native_languages,
person_position,
previous_activities,
project,
project_additional_contact_persons,
project_archived_directories,
project_categories,
project_language_combination,
project_resource,
provider,
provider_billing_data,
provider_categories,
provider_charge,
provider_invoice,
provider_invoice_categories,
provider_language_combination,
provider_payment,
provider_payment_item,
provider_person,
province,
quote,
quote_additional_contact_persons,
quote_categories,
quote_language_combination,
rejection_reason,
service,
system_account,
task,
task_additional_contact_persons,
task_amount_modifiers,
task_cat_charge,
task_cat_charge_amount_modifiers,
task_categories,
task_charge,
task_charge_amount_modifiers,
task_finance,
task_review,
task_workflow_job_instance,
tm_rates,
tm_rates_item,
tm_savings,
tm_savings_item,
vat_rate,
workflow,
workflow_job,
workflow_job_file,
workflow_job_instance,
workflow_job_phase,
xtrf_currency,
xtrf_language,
xtrf_user,
xtrf_user_group RESTART IDENTITY;

ALTER TABLE
	analytics_project DROP CONSTRAINT FK_85115C10166D1F9C;

ALTER TABLE
	analytics_project DROP CONSTRAINT FK_85115C108DB60186;

ALTER TABLE
	analytics_project DROP CONSTRAINT FK_85115C1081C06096;

ALTER TABLE
	analytics_project DROP CONSTRAINT FK_85115C10CE6064C2;

ALTER TABLE
	analytics_project_step DROP CONSTRAINT FK_A4B70CF6CE6064C2;

ALTER TABLE
	token DROP CONSTRAINT FK_5F37A13BA76ED395;

ALTER TABLE
	permission DROP CONSTRAINT FK_E04992AAD60322AC;

ALTER TABLE
	permission DROP CONSTRAINT FK_E04992AA9D32F035;

ALTER TABLE
	permission DROP CONSTRAINT FK_E04992AAA76ED395;

ALTER TABLE
	dqa_report DROP CONSTRAINT FK_606FF4B6AC74095A;

ALTER TABLE
	cp_setting DROP CONSTRAINT FK_A7E7925C81398E09;

ALTER TABLE
	qbo_payment_item DROP CONSTRAINT FK_C6F330312727A733;

ALTER TABLE
	qbo_provider_payment DROP CONSTRAINT FK_56A2A9A5D212BD4F;

ALTER TABLE
	qbo_customer_payment DROP CONSTRAINT FK_BA27AFBC2727A733;

ALTER TABLE
	qbo_customer_payment DROP CONSTRAINT FK_BA27AFBCA6DF575;

ALTER TABLE
	qbo_provider_invoice DROP CONSTRAINT FK_ABEF3AEC46C2769C;

ALTER TABLE
	qbo_customer_invoice_item DROP CONSTRAINT FK_826F5A05D440F57F;

ALTER TABLE
	permission DROP CONSTRAINT FK_E04992AA9FFDF951;

ALTER TABLE
	cp_template DROP CONSTRAINT FK_B518A004F8A983C;

ALTER TABLE 
	hs_contact_person DROP CONSTRAINT FK_55056594F8A983C ;
	
ALTER TABLE 
	hs_contact_person DROP CONSTRAINT FK_55056597E3C61F9 ;

ALTER TABLE 
	hs_customer DROP CONSTRAINT FK_D51E27CD9395C3F3;

ALTER TABLE 
	hs_customer DROP CONSTRAINT FK_D51E27CD7E3C61F9;

ALTER TABLE hs_deal DROP CONSTRAINT FK_D07AE4837E3C61F9;

/*******************
 ADD CONSTRAINTS
 ********************/
ALTER TABLE
	analytics_project
ADD
	CONSTRAINT FK_85115C10166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	analytics_project
ADD
	CONSTRAINT FK_85115C108DB60186 FOREIGN KEY (task_id) REFERENCES task (task_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	analytics_project
ADD
	CONSTRAINT FK_85115C1081C06096 FOREIGN KEY (activity_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	analytics_project
ADD
	CONSTRAINT FK_85115C10CE6064C2 FOREIGN KEY (xtrf_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	analytics_project
ADD
	CONSTRAINT FK_85115C1082F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	token
ADD
	CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	permission
ADD
	CONSTRAINT FK_E04992AAD60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	permission
ADD
	CONSTRAINT FK_E04992AA9D32F035 FOREIGN KEY (action_id) REFERENCES action (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	permission
ADD
	CONSTRAINT FK_E04992AAA76ED395 FOREIGN KEY (user_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	dqa_report
ADD
	CONSTRAINT FK_606FF4B6AC74095A FOREIGN KEY (activity_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	cp_setting
ADD
	CONSTRAINT FK_A7E7925C81398E09 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	qbo_payment_item
ADD
	CONSTRAINT FK_C6F330312727A733 FOREIGN KEY (xtrf_customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	qbo_customer_payment
ADD
	CONSTRAINT FK_BA27AFBCA6DF575 FOREIGN KEY (xtrf_customer_payment_id) REFERENCES customer_payment (customer_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	qbo_customer_payment
ADD
	CONSTRAINT FK_BA27AFBC2727A733 FOREIGN KEY (xtrf_customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	qbo_provider_payment
ADD
	CONSTRAINT FK_56A2A9A5D212BD4F FOREIGN KEY (xtrf_provider_payment_id) REFERENCES provider_payment (provider_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	qbo_provider_invoice
ADD
	CONSTRAINT FK_ABEF3AEC46C2769C FOREIGN KEY (xtrf_provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	qbo_customer_invoice_item
ADD
	CONSTRAINT FK_826F5A05D440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	permission
ADD
	CONSTRAINT FK_E04992AA9FFDF951 FOREIGN KEY (cp_user_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE
	cp_template
ADD
	CONSTRAINT FK_B518A004F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE hs_contact_person ADD CONSTRAINT FK_55056594F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE hs_contact_person ADD CONSTRAINT FK_55056597E3C61F9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE hs_customer ADD CONSTRAINT FK_D51E27CD9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE hs_customer ADD CONSTRAINT FK_D51E27CD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE hs_deal ADD CONSTRAINT FK_D07AE4837E3C61F9 FOREIGN KEY (owner_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;


--- DISABLE TRIGGERS
ALTER TABLE
	task DISABLE TRIGGER a_task_margin_trigger;

ALTER TABLE
	task DISABLE TRIGGER c_task_time_based_cost_trigger;

ALTER TABLE
	task_finance DISABLE TRIGGER task_finance_total_agreed_trigger;

ALTER TABLE
	custom_fields DISABLE TRIGGER main_custom_fields_triggers;

ALTER TABLE
	tm_savings_item DISABLE TRIGGER tm_saving_items_total_trigger;

ALTER TABLE
	task_cat_charge DISABLE TRIGGER tm_saving_items_total_task_cat_charge_trigger;

ALTER TABLE
	task_cat_charge DISABLE TRIGGER tm_saving_items_trigger;

ALTER TABLE
	project DISABLE TRIGGER project_trigger;

ALTER TABLE
	quote DISABLE TRIGGER quote_trigger;

ALTER TABLE
	workflow_job_file DISABLE TRIGGER job_files_trigger;

ALTER TABLE
	activity DISABLE TRIGGER activity_close_date_trigger;

ALTER TABLE
	task_charge DISABLE TRIGGER total_value_trigger;

ALTER TABLE
	workflow_job DISABLE TRIGGER activity_name_trigger;

--- ADD TRIGGERS AGAIN
ALTER TABLE
	task ENABLE ALWAYS TRIGGER a_task_margin_trigger;

ALTER TABLE
	task ENABLE ALWAYS TRIGGER c_task_time_based_cost_trigger;

ALTER TABLE
	task_finance ENABLE ALWAYS TRIGGER task_finance_total_agreed_trigger;

ALTER TABLE
	custom_fields ENABLE ALWAYS TRIGGER main_custom_fields_triggers;

ALTER TABLE
	tm_savings_item ENABLE ALWAYS TRIGGER tm_saving_items_total_trigger;

ALTER TABLE
	task_cat_charge ENABLE ALWAYS TRIGGER tm_saving_items_total_task_cat_charge_trigger;

ALTER TABLE
	task_cat_charge ENABLE ALWAYS TRIGGER tm_saving_items_trigger;

ALTER TABLE
	project ENABLE ALWAYS TRIGGER project_trigger;

ALTER TABLE
	quote ENABLE ALWAYS TRIGGER quote_trigger;

ALTER TABLE
	workflow_job_file ENABLE ALWAYS TRIGGER job_files_trigger;

ALTER TABLE
	activity ENABLE ALWAYS TRIGGER activity_close_date_trigger;

ALTER TABLE
	task_charge ENABLE ALWAYS TRIGGER total_value_trigger;

ALTER TABLE
	workflow_job ENABLE ALWAYS TRIGGER activity_name_trigger;
