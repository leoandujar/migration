-- SYNCRONIZE THE PROGRESS FIELDS FOR TASK AND PROJECTS
CREATE OR REPLACE FUNCTION syncronize_progress_activity_func() RETURNS VOID AS
$$
declare
    activitiesCount    numeric;
    activitiesProgress numeric;
    row                record;
BEGIN
    FOR row IN SELECT * FROM task
        loop
            activitiesCount := 0;
            activitiesProgress := 0;
            SELECT COUNT(a.activity_id) FROM activity a WHERE a.task_id = row.task_id INTO activitiesCount;
            SELECT COUNT(a.activity_id) FROM activity a WHERE a.task_id = row.task_id and a.status = 'READY' INTO activitiesProgress;
            UPDATE task SET total_activities = activitiesCount WHERE task_id = row.task_id;
            UPDATE task SET progress_activities = activitiesProgress WHERE task_id = row.task_id;
        end loop;
    FOR row IN SELECT * FROM project
        loop
            activitiesCount := 0;
            activitiesProgress := 0;
            SELECT SUM(t.total_activities) FROM task t WHERE t.project_id = row.project_id INTO activitiesCount;
            SELECT SUM(t.progress_activities) FROM task t WHERE t.project_id = row.project_id INTO activitiesProgress;
            UPDATE project SET total_activities = activitiesCount WHERE project_id = row.project_id;
            UPDATE project SET progress_activities = activitiesProgress WHERE project_id = row.project_id;
        end loop;
END;
$$ LANGUAGE plpgsql;

-- SYNCRONIZE DATA FOR FUNCTION activity_close_date_func
CREATE OR REPLACE FUNCTION syncronize_activity_close_date_func() RETURNS VOID AS
$$
declare
    wfj                 record;
    row                 record;
    closeDate           timestamp;
    taskDeadLine        timestamp;
    onTimeStatus        numeric;
    activityId          bigint;
    quoteId             bigint;
    taskId              bigint;
    jobName             varchar;
    activityTypeName    text;
    sumTotalAgreed      numeric;
    sumTotalAgreedQuote numeric;

    projectId                  numeric;
    activitiesCountTasks       numeric;
    activitiesProgressTasks    numeric;
    activitiesCountProjects    numeric;
    activitiesProgressProjects numeric;
BEGIN
    FOR row IN (SELECT * FROM activity where activity_id = 589075)
        loop
            closeDate = row.close_date;
            taskId = row.task_id;
            activityId = row.activity_id;

            FOR wfj IN SELECT workflow_job.name, workflow_job.workflow_job_id
                       FROM activity
                                INNER JOIN workflow_job_instance ON activity.workflow_job_instance_id = workflow_job_instance.workflow_job_instance_id
                                INNER JOIN workflow_job ON workflow_job_instance.workflow_job_id = workflow_job.workflow_job_id
                       WHERE activity_id = activityId
                LOOP
                    jobName = wfj.name;
                    UPDATE activity SET activity_name = jobName WHERE activity_id = activityId;
                end loop;

            SELECT at.name
            FROM activity a
                     INNER JOIN activity_type at ON a.activity_type_id = at.activity_type_id
            WHERE a.activity_id = activityId
            INTO activityTypeName;
            IF activityTypeName = 'Partial Delivery' THEN
                IF closeDate IS NOT NULL THEN
                    SELECT t.deadline FROM task t WHERE t.task_id = taskId INTO taskDeadLine;
                    closeDate = closeDate::timestamp;
                    taskDeadLine = taskDeadLine::timestamp;
                    onTimeStatus = EXTRACT(EPOCH FROM closeDate - taskDeadLine);
                END IF;
                UPDATE task SET partial_delivery_date = closeDate WHERE task_id = taskId;
                UPDATE task SET ontime_status = onTimeStatus WHERE task_id = taskId;
            END IF;

            SELECT SUM(a.total_agreed)
            FROM activity a
                     INNER JOIN task ON a.task_id = task.task_id
            WHERE a.task_id = taskId
            INTO sumTotalAgreed;
            UPDATE task SET total_cost = sumTotalAgreed WHERE task_id = taskId;

            SELECT SUM(a.total_agreed)
            FROM activity a
                     INNER JOIN task ON a.task_id = task.task_id
            WHERE a.task_id = taskId
              AND a.quote_phase_id_number IS NOT NULL
            INTO sumTotalAgreedQuote;
            SELECT quote_id FROM task where task.task_id = taskId INTO quoteId;
            UPDATE quote SET total_cost = sumTotalAgreedQuote WHERE quote_id = quoteId;

            RAISE NOTICE 'TODO BOMBIIII';
            activitiesCountTasks := 0;
            activitiesProgressTasks := 0;
            SELECT project_id FROM task t WHERE t.task_id = taskId INTO projectId;
            SELECT COUNT(a.activity_id) FROM activity a WHERE a.task_id = taskId INTO activitiesCountTasks;
            SELECT COUNT(a.activity_id) FROM activity a WHERE a.task_id = taskId and a.status = 'READY' INTO activitiesProgressTasks;
            UPDATE task SET total_activities = activitiesCountTasks WHERE task_id = taskId;
            UPDATE task SET progress_activities = activitiesProgressTasks WHERE task_id = taskId;

            SELECT SUM(t.total_activities) FROM task t WHERE t.project_id = projectId INTO activitiesCountProjects;
            SELECT SUM(t.progress_activities) FROM task t WHERE t.project_id = projectId INTO activitiesProgressProjects;
            UPDATE project SET total_activities = activitiesCountProjects WHERE project_id = projectId;
            UPDATE project SET progress_activities = activitiesProgressProjects WHERE project_id = projectId;
        end loop;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION syncronize_time_based_cost_func() RETURNS VOID AS
$$
declare
    sumTotalValue numeric;
    row           record;
BEGIN
    FOR row IN SELECT *
               FROM activity_charge ach
                        INNER JOIN activity a ON ach.activity_id = a.activity_id
                        INNER JOIN calculation_unit cu ON ach.calculation_unit_id = cu.calculation_unit_id
                        INNER JOIN task t ON a.task_id = t.task_id
               WHERE cu.type = 'TIME'
        loop
            SELECT SUM(CASE
                           WHEN ach.ignore_minimal_charge = true or ach.minimal_charge IS NULL
                               THEN
                                   ach.quantity * ach.rate * ach.total_amount_modifier
                           WHEN (ach.quantity * ach.rate) > ach.minimal_charge AND
                                ach.ignore_minimal_charge = false AND
                                ach.minimal_charge IS NOT NULL
                               THEN
                                   ach.quantity * ach.rate * ach.total_amount_modifier
                           ELSE
                                   ach.minimal_charge * ach.total_amount_modifier
                END) AS total_value
            FROM activity_charge ach
                     INNER JOIN activity a ON ach.activity_id = a.activity_id
                     INNER JOIN task t ON a.task_id = t.task_id
                     INNER JOIN calculation_unit cu ON ach.calculation_unit_id = cu.calculation_unit_id
            WHERE t.task_id = row.task_id
              AND cu.type = 'TIME'
            INTO sumTotalValue;
            UPDATE task SET time_based_cost = sumTotalValue WHERE task_id = row.task_id;
        end loop;

END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_total_agreed_func() RETURNS VOID AS
$$
declare
    tfRow       record;
    quoteRow    record;
    row         record;
    taskId      bigint;
    taskQuoteId bigint;
BEGIN
    FOR row IN SELECT * FROM task_finance
        loop
            SELECT task.task_id
            FROM task_finance tf
                     INNER JOIN task ON tf.task_finance_id = task.project_part_finance_id
            WHERE task.project_part_finance_id = row.task_finance_id
            INTO taskId;
            SELECT task.quote_id
            FROM task_finance tf
                     INNER JOIN task ON tf.task_finance_id = task.quote_part_finance_id
            WHERE task.quote_part_finance_id = row.task_finance_id
            INTO taskQuoteId;

            IF taskQuoteId IS NOT NULL THEN
                SELECT * from quote where quote_id = taskQuoteId INTO quoteRow;
                IF quoteRow.status = 'REJECTED' THEN
                    FOR tfRow IN SELECT SUM(taskf.total_agreed) as sumTotalAgree
                                 from quote q
                                          INNER JOIN task t on t.quote_id = q.quote_id
                                          INNER JOIN task_finance taskf on taskf.task_finance_id = t.quote_part_finance_id
                                 WHERE q.quote_id = taskQuoteId
                        LOOP
                            UPDATE quote SET total_agreed = tfRow.sumTotalAgree WHERE quote_id = taskQuoteId;
                        END LOOP;
                ELSE
                    FOR tfRow IN SELECT SUM(taskf.total_agreed) as sumTotalAgree
                                 from quote q
                                          INNER JOIN task t on t.quote_id = q.quote_id
                                          INNER JOIN task_finance taskf on taskf.task_finance_id = t.quote_part_finance_id
                                 WHERE q.quote_id = taskQuoteId
                                   AND q.status <> 'CANCELLED'
                        LOOP
                            UPDATE quote SET total_agreed = tfRow.sumTotalAgree WHERE quote_id = taskQuoteId;
                        END LOOP;
                END IF;
            END IF;
            UPDATE task SET total_agreed = row.total_agreed WHERE task.task_id = taskId;
            UPDATE task SET total_amount_modifier = row.total_amount_modifier WHERE task.task_id = taskId;
        END LOOP;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_task_total_cost_func() RETURNS VOID AS
$$
declare
    row            record;
    targetRecord   record;
    closeDate      timestamp;
    activityId     bigint;
    taskId         bigint;
    sumTotalAgreed numeric;
BEGIN
    FOR row IN SELECT * FROM activity
        loop
            closeDate = row.close_date;
            taskId = row.task_id;
            activityId = row.activity_id;
            targetRecord = row;

            SELECT SUM(a.total_agreed)
            FROM activity a
                     INNER JOIN task ON a.task_id = task.task_id
            WHERE a.task_id = taskId
            INTO sumTotalAgreed;
            UPDATE task SET total_cost = sumTotalAgreed WHERE task_id = taskId;

        END LOOP;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_custom_fields_func() RETURNS VOID AS
$$
declare
    row             record;
    rowRow          record;
    --VALUE OF OWNER TYPE COLUMN...ITS THE LINK TO CUSTOM_FIELD_CONFIGURATION
    ownerType       varchar;
    tableDestiny    varchar;
    columnNameCamel varchar;
    customFieldKey  varchar;
    firstCharacter  varchar;
    sqlStm          text;
BEGIN
    -- ITERATE OVER ALL CHANGES COLUMNS IN THE ROW
    FOR row IN SELECT * from custom_fields
        LOOP
            FOR rowRow IN SELECT (each(hstore(hstore(row)))).*
                loop
                    ownertype := row.ownertype;
                    -- MAKE EXCEPTION FOR OWNER NAME DIFF TO FIELD NAME
                    IF ownertype LIKE 'person' THEN
                        ownerType = 'contact_person';
                    END IF;
                    ownertype := upper(ownerType);
                    -- CONVERT THE CURRENT COLUMN NAME TO CAPITALIZE ALL LETTERS DUE POSTGRES RETURN IT IN SNAKE MODE (EG.. text_field_1, RESULT: TextField1)
                    columnNameCamel = replace(initcap(rowRow.key), '_', '');
                    -- TAKE THE FIRST CHARACTER OF THE CAPITALIZED COLUMN NAME, FOR CONVERTING IN LOWER CASE LATER(EG.. for TextField1 Result: T)
                    firstCharacter = substr(columnNameCamel, 1, 1);
                    -- REPLACE THE FIRST CHARACTER FOR THE LOWER CASE IN ORDER TO OBTAIN THE REAL CAMEL CASE COLUMN NAME (EG.. for TextField1 RESULT: textField1)
                    columnNameCamel = replace(columnNameCamel, firstCharacter, lower(firstCharacter));
                    -- FIND THE VALUE OF KEY COLUMN IN CONFIGURATION TABLE BY THE CRITERIA OF OWNER TYPE AND COLUMN NAME.
                    SELECT key FROM custom_field_configuration WHERE "fields_names"::TEXT LIKE concat('%', '"', ownerType, '"', ': ', '"', columnNameCamel, '"', '%') INTO customFieldKey;
                    -- MAKING SURE THE CHANGED COLUMN HAS ENTRY IN CUSTOM FIELD CONFIGURATION...OTHERWISE WE SHOULD IGNORE THE CHANGES.
                    IF lower(ownerType) LIKE 'user' THEN
                        tableDestiny = 'xtrf_user';
                    ELSE
                        tableDestiny = lower(ownerType);
                    END IF;
                    IF customFieldKey is not null THEN
                        sqlStm = 'UPDATE ' || quote_ident(tableDestiny) || ' SET ' || quote_ident(customFieldKey) || '= $1 WHERE custom_fields_id = $2';
                        -- CHECKING THE PROPER TYPE
                        IF rowRow.key like 'checkbox_%' THEN
                            EXECUTE sqlStm USING bool(rowRow.value) ,row.custom_fields_id;
                        ELSEIF rowRow.key like 'date_%' THEN
                            EXECUTE sqlStm USING to_timestamp(rowRow.value, '''YYYY-MM-DD HH24:MI:SS') ,row.custom_fields_id;
                        ELSEIF rowRow.key like 'multi_select_%' THEN
                            EXECUTE sqlStm USING text(rowRow.value) ,row.custom_fields_id;
                        ELSEIF rowRow.key like 'number_%' THEN
                            EXECUTE sqlStm USING CAST(rowRow.value AS numeric) ,row.custom_fields_id;
                        ELSEIF rowRow.key like 'select_%' THEN
                            EXECUTE sqlStm USING text(rowRow.value) ,row.custom_fields_id;
                        ELSEIF rowRow.key like 'text_%' THEN
                            EXECUTE sqlStm USING text(rowRow.value) ,row.custom_fields_id;
                        END IF;
                    END IF;
                end loop;
        end loop;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_total_value_func() RETURNS VOID AS
$$
declare
    row           record;
    totalValueSum numeric;
BEGIN
    FOR row IN SELECT * from task_charge
        LOOP
            SELECT CASE
                       WHEN task_charge.ignore_minimal_charge = true or task_charge.minimal_charge IS NULL
                           THEN task_charge.quantity * task_charge.rate * task_charge.total_amount_modifier
                       WHEN (task_charge.quantity * task_charge.rate) > task_charge.minimal_charge AND task_charge.ignore_minimal_charge = false AND task_charge.minimal_charge IS NOT NULL
                           THEN task_charge.quantity * task_charge.rate * task_charge.total_amount_modifier
                       ELSE task_charge.minimal_charge * task_charge.total_amount_modifier
                       END
                       AS total_value
            FROM task_charge
            WHERE task_charge_id = row.task_charge_id
            INTO totalValueSum;
            UPDATE task_charge SET total_value = totalValueSum WHERE task_charge_id = row.task_charge_id;
        end loop;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_minimum_task_func() RETURNS VOID AS
$$
declare
    row    record;
    taskId bigint;
BEGIN
    FOR row IN SELECT * from task_finance
        LOOP
            SELECT task.task_id
            FROM task_finance tf
                     INNER JOIN task ON tf.task_finance_id = task.project_part_finance_id
            WHERE task.project_part_finance_id = row.task_finance_id
            INTO taskId;
            IF row.ignore_minimal_charge = false and row.minimal_charge = row.total_agreed THEN
                UPDATE task SET minimum = true WHERE task.task_id = taskId;
            ELSE
                UPDATE task SET minimum = false WHERE task.task_id = taskId;
            END IF;
        end loop;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_activity_name_func() RETURNS VOID AS
$$
declare
    row        record;
    act        record;
    activityId bigint;
BEGIN
    FOR row IN SELECT * from workflow_job
        LOOP
            FOR act IN SELECT activity_id
                       FROM workflow_job INNER JOIN workflow_job_instance ON workflow_job.workflow_job_id = workflow_job_instance.workflow_job_id
                                         INNER JOIN activity ON workflow_job_instance.workflow_job_instance_id = activity.workflow_job_instance_id
                       WHERE workflow_job.workflow_job_id = row.workflow_job_id
                LOOP
                    activityId = act.activity_id;
                    UPDATE activity SET activity_name = row.name WHERE activity_id = activityId;
                end loop;
        end loop;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_tm_saving_items_total_func() RETURNS VOID AS
$$
declare
    taskCatChargeId     bigint;
    sumTotalValue       numeric;
    sumTotalQuantity    numeric;
    totalAmountModifier numeric;
    rateValue           numeric;
    row                 record;
    targetRecord        record;
BEGIN
    FOR row IN SELECT * FROM tm_savings_item
        loop
            SELECT SUM(tmi.fixed_rate * tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = row.tm_savings_id INTO sumTotalValue;
            SELECT total_amount_modifier FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = row.tm_savings_id INTO totalAmountModifier;
            UPDATE task_cat_charge tcch SET total_value = sumTotalValue * totalAmountModifier WHERE tcch.tm_savings_id = row.tm_savings_id;

            SELECT rate FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = row.tm_savings_id INTO rateValue;
            UPDATE task_cat_charge tcch SET weighted_quantity = sumTotalValue / (coalesce(rateValue, 0)) WHERE tcch.tm_savings_id = row.tm_savings_id;
            SELECT SUM(tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = row.tm_savings_id INTO sumTotalQuantity;
            UPDATE task_cat_charge tcch SET total_quantity = sumTotalQuantity WHERE tcch.tm_savings_id = row.tm_savings_id;
            SELECT task_finance_id FROM task_cat_charge tcch WHERE tcch.tm_savings_id = row.tm_savings_id INTO taskCatChargeId;

            SELECT SUM(tmi.fixed_rate * tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = row.tm_savings_id INTO sumTotalValue;
            SELECT total_amount_modifier FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = row.tm_savings_id INTO totalAmountModifier;
            UPDATE task_cat_charge tcch SET total_value = sumTotalValue * totalAmountModifier WHERE tcch.tm_savings_id = row.tm_savings_id;
            SELECT rate FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = row.tm_savings_id INTO rateValue;
            UPDATE task_cat_charge tcch SET weighted_quantity = sumTotalValue / (coalesce(rateValue, 0)) WHERE tcch.tm_savings_id = row.tm_savings_id;
        end loop;
    RETURN targetRecord;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION syncronize_working_files_func() RETURNS VOID AS
$$
declare
    row        record;
    taskId bigint;
    totalCount numeric;
BEGIN
    FOR row IN SELECT * from task
        LOOP
            taskId = row.task_id;
            SELECT COUNT(*) FROM workflow_job_file wjf WHERE wjf.task_id = taskId AND wjf.category = 'WORKFILE' INTO totalCount;
            UPDATE task SET working_files_number = totalCount WHERE task_id = taskId;
        end loop;
END;
$$ LANGUAGE plpgsql;


-- NEW FUNCTION FOR JSON DIFF, REPLACEMENT OF HSTORE
create or replace function jsonb_diff(jsonb, jsonb)
    returns jsonb language sql immutable as $$
select jsonb_object_agg(n.key, n.value)
from jsonb_each($1) o
         join jsonb_each($2) n on o.key = n.key
where o.value <> n.value;
$$;


select * from syncronize_progress_activity_func();
select * from syncronize_time_based_cost_func();
select * from syncronize_total_agreed_func();
select * from syncronize_task_total_cost_func();
select * from syncronize_custom_fields_func();
select * from syncronize_total_value_func();
select * from syncronize_minimum_task_func();
select * from syncronize_activity_name_func();
select * from syncronize_tm_saving_items_total_func();
select * from syncronize_working_files_func();
