CREATE TRIGGER main_custom_fields_triggers
    AFTER UPDATE OR INSERT
    ON custom_fields
    FOR EACH ROW
EXECUTE PROCEDURE main_custom_fields_func();
ALTER TABLE custom_fields
    ENABLE ALWAYS TRIGGER main_custom_fields_triggers;

CREATE TRIGGER task_finance_total_agreed_trigger
    AFTER INSERT OR UPDATE OR DELETE
    ON task_finance
    FOR EACH ROW
EXECUTE PROCEDURE task_finance_total_agreed_func();
ALTER TABLE task_finance
    ENABLE ALWAYS TRIGGER task_finance_total_agreed_trigger;

CREATE TRIGGER a_task_margin_trigger
    AFTER INSERT OR UPDATE OR DELETE
    ON task
    FOR EACH ROW
EXECUTE PROCEDURE a_task_margin_func();
ALTER TABLE task
    ENABLE ALWAYS TRIGGER a_task_margin_trigger;

CREATE TRIGGER c_task_time_based_cost_trigger
    AFTER INSERT OR UPDATE
    ON task
    FOR EACH ROW
EXECUTE PROCEDURE c_task_time_based_cost_func();
ALTER TABLE task
    ENABLE ALWAYS TRIGGER c_task_time_based_cost_trigger;

CREATE TRIGGER tm_saving_items_total_trigger
    AFTER UPDATE OR DELETE
    ON tm_savings_item
    FOR EACH ROW
EXECUTE PROCEDURE tm_saving_items_total_func();
ALTER TABLE tm_savings_item
    ENABLE ALWAYS TRIGGER tm_saving_items_total_trigger;

CREATE TRIGGER tm_saving_items_total_task_cat_charge_trigger
    AFTER INSERT
    ON task_cat_charge
    FOR EACH ROW
EXECUTE PROCEDURE tm_saving_items_total_task_cat_charge_func();
ALTER TABLE task_cat_charge
    ENABLE ALWAYS TRIGGER tm_saving_items_total_task_cat_charge_trigger;

CREATE TRIGGER tm_saving_items_trigger
    AFTER INSERT OR UPDATE OR DELETE
    ON task_cat_charge
    FOR EACH ROW
EXECUTE PROCEDURE tm_saving_items_func();
ALTER TABLE task_cat_charge
    ENABLE ALWAYS TRIGGER tm_saving_items_trigger;

CREATE TRIGGER project_trigger
    AFTER INSERT OR UPDATE
    ON project
    FOR EACH ROW
EXECUTE PROCEDURE project_func();
ALTER TABLE project
    ENABLE ALWAYS TRIGGER project_trigger;

CREATE TRIGGER quote_trigger
    AFTER INSERT OR UPDATE
    ON quote
    FOR EACH ROW
EXECUTE PROCEDURE quote_func();
ALTER TABLE quote
    ENABLE ALWAYS TRIGGER quote_trigger;

CREATE TRIGGER job_files_trigger
    AFTER INSERT OR DELETE
    ON workflow_job_file
    FOR EACH ROW
EXECUTE PROCEDURE job_file_func();
ALTER TABLE workflow_job_file
    ENABLE ALWAYS TRIGGER job_files_trigger;

CREATE TRIGGER activity_close_date_trigger
    AFTER INSERT OR UPDATE OR DELETE
    ON activity
    FOR EACH ROW
EXECUTE PROCEDURE activity_close_date_func();
ALTER TABLE activity
    ENABLE ALWAYS TRIGGER activity_close_date_trigger;

CREATE TRIGGER total_value_trigger
    AFTER UPDATE OR INSERT
    ON task_charge
    FOR EACH ROW
EXECUTE PROCEDURE total_value_func();
ALTER TABLE task_charge
    ENABLE ALWAYS TRIGGER total_value_trigger;

CREATE TRIGGER activity_name_trigger
    AFTER UPDATE OR INSERT
    ON workflow_job
    FOR EACH ROW
EXECUTE PROCEDURE activity_name_func();
ALTER TABLE workflow_job
    ENABLE ALWAYS TRIGGER activity_name_trigger;

CREATE TRIGGER customers_create_setup_trigger
    AFTER INSERT
    ON customer
    FOR EACH ROW
EXECUTE PROCEDURE customers_create_setup_func();
ALTER TABLE customer
    ENABLE ALWAYS TRIGGER customers_create_setup_trigger;

CREATE TRIGGER entity_lower_trigger
    AFTER INSERT OR UPDATE ON contact_person
    FOR EACH ROW
EXECUTE PROCEDURE entity_lower_func();
ALTER TABLE contact_person ENABLE ALWAYS TRIGGER entity_lower_trigger;
