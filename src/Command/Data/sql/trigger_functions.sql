CREATE OR REPLACE FUNCTION main_custom_fields_func() RETURNS TRIGGER AS
$$
declare
    row             record;
    --VALUE OF OWNER TYPE COLUMN...ITS THE LINK TO CUSTOM_FIELD_CONFIGURATION
    ownerType       varchar;
    tableDestiny    varchar;
    columnNameCamel varchar;
    customFieldKey  varchar;
    firstCharacter  varchar;
    sqlStm          text;
BEGIN
    SET search_path TO public;
    -- ITERATE OVER ALL CHANGES COLUMNS IN THE ROW
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            ownertype := new.ownertype;
            -- MAKE EXCEPTION FOR OWNER NAME DIFF TO FIELD NAME
            IF ownertype LIKE 'person' THEN
                ownerType = 'contact_person';
            END IF;
            ownertype := upper(ownerType);
            -- CONVERT THE CURRENT COLUMN NAME TO CAPITALIZE ALL LETTERS DUE POSTGRES RETURN IT IN SNAKE MODE (EG.. text_field_1, RESULT: TextField1)
            columnNameCamel = replace(initcap(row.key), '_', '');
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
                IF row.key like 'checkbox_%' THEN
                    EXECUTE sqlStm USING bool(row.value) ,new.custom_fields_id;
                ELSEIF row.key like 'date_%' THEN
                    EXECUTE sqlStm USING to_timestamp(row.value, '''YYYY-MM-DD HH24:MI:SS') ,new.custom_fields_id;
                ELSEIF row.key like 'multi_select_%' THEN
                    EXECUTE sqlStm USING text(row.value) ,new.custom_fields_id;
                ELSEIF row.key like 'number_%' THEN
                    EXECUTE sqlStm USING CAST(row.value AS numeric) ,new.custom_fields_id;
                ELSEIF row.key like 'select_%' THEN
                    EXECUTE sqlStm USING text(row.value) ,new.custom_fields_id;
                ELSEIF row.key like 'text_%' THEN
                    EXECUTE sqlStm USING text(row.value) ,new.custom_fields_id;
                END IF;
            END IF;
        end loop;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION task_finance_total_agreed_func() RETURNS TRIGGER AS
$task_finance_total_agreed_func$
declare
    tfRow       record;
    quoteRow       record;
    row         record;
    taskId      bigint;
    taskQuoteId bigint;
BEGIN
    SET search_path TO public;
    taskId = NEW.task_id;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF row.key = 'total_agreed' THEN
                SELECT task.task_id
                FROM task_finance tf
                         INNER JOIN task ON tf.task_finance_id = task.project_part_finance_id
                WHERE task.project_part_finance_id = NEW.task_finance_id
                INTO taskId;

                SELECT task.quote_id
                FROM task_finance tf
                         INNER JOIN task ON tf.task_finance_id = task.quote_part_finance_id
                WHERE task.quote_part_finance_id = NEW.task_finance_id
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
                                     WHERE q.quote_id = taskQuoteId AND t.status <> 'CANCELLED'
                            LOOP
                                UPDATE quote SET total_agreed = tfRow.sumTotalAgree WHERE quote_id = taskQuoteId;
                            END LOOP;
                    END IF;
                END IF;
                UPDATE task SET total_agreed = NEW.total_agreed WHERE task.task_id = taskId;
            ELSEIF row.key = 'total_amount_modifiers' THEN
                SELECT task.task_id
                FROM task_finance tf
                         INNER JOIN task ON tf.task_finance_id = task.project_part_finance_id
                WHERE task.project_part_finance_id = NEW.task_finance_id
                INTO taskId;
                UPDATE task SET total_amount_modifier = NEW.total_amount_modifier WHERE task.task_id = taskId;
            ELSEIF row.key = 'minimal_charge' OR row.key = 'ignore_minimal_charge' THEN
                IF NEW.ignore_minimal_charge = false and NEW.minimal_charge = NEW.total_agreed THEN
                    SELECT task.task_id
                    FROM task_finance tf
                             INNER JOIN task ON tf.task_finance_id = task.project_part_finance_id
                    WHERE task.project_part_finance_id = NEW.task_finance_id
                    INTO taskId;
                    UPDATE task SET minimum = true WHERE task.task_id = taskId;
                end if;
            END IF;
        END LOOP;
    RETURN NEW;
END;
$task_finance_total_agreed_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION a_task_margin_func() RETURNS TRIGGER AS
$a_task_margin_func$
declare
    row                      record;
    projectId                bigint;
    quoteId                  bigint;
    totalAgreedTask          numeric;
    totalAgreedProjectSum    numeric;
    totalCostProjectSum      numeric;
    totalCostQuoteSum        numeric;
    totalBasedCostProjectSum numeric;
    totalTmSavingsProjectSum numeric;
    targetRecord             record;
    sumMargin                numeric;
BEGIN
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
                projectId = NEW.project_id;
                quoteId = NEW.quote_id;
                targetRecord = NEW;
            ELSE
                projectId = OLD.project_id;
                quoteId = OLD.quote_id;
                targetRecord = OLD;
            END IF;

            IF row.key = 'total_cost' THEN
                -- TASK TABLE
                sumMargin = (1 - targetRecord.total_cost / NULLIF(targetRecord.total_agreed, 0));
                UPDATE task SET margin = sumMargin WHERE task_id = targetRecord.task_id;
                IF (targetRecord.total_cost <> 0.00) THEN
                    UPDATE task SET rentability = sumMargin * targetRecord.total_agreed / (NULLIF(targetRecord.total_cost, 0)) WHERE task_id = targetRecord.task_id;
                END IF;

                -- PROJECT TABLE
                SELECT SUM(t.total_cost) FROM task t WHERE t.project_id = projectId INTO totalCostProjectSum;
                UPDATE project SET total_cost = totalCostProjectSum WHERE project_id = projectId;

                -- QUOTE TABLE
                SELECT SUM(t.total_cost) FROM task t WHERE t.quote_id = quoteId INTO totalCostQuoteSum;
                UPDATE quote SET total_cost = totalCostQuoteSum WHERE quote_id = quoteId;
            ELSEIF row.key = 'total_agreed' THEN
                -- TASK TABLE
                sumMargin = (1 - targetRecord.total_cost / (NULLIF(targetRecord.total_agreed, 0)));
                UPDATE task SET margin = sumMargin WHERE task_id = targetRecord.task_id;
                if (targetRecord.total_cost * 100 <> 0.00) THEN
                    UPDATE task SET rentability = sumMargin * targetRecord.total_agreed / (NULLIF(targetRecord.total_cost, 0)) WHERE task_id = targetRecord.task_id;
                END IF;

                -- PROJECT TABLE
                SELECT SUM(t.total_agreed) FROM task t WHERE t.project_id = projectId INTO totalAgreedProjectSum;
                UPDATE project SET total_agreed = totalAgreedProjectSum WHERE project_id = projectId;
            ELSEIF row.key = 'project_id' THEN
                SELECT tf.total_agreed
                FROM task_finance tf
                            INNER JOIN task ON tf.task_finance_id = task.project_part_finance_id
                WHERE task.project_part_finance_id = NEW.project_part_finance_id
                INTO totalAgreedTask;
                UPDATE task SET total_agreed = totalAgreedTask WHERE task.task_id = NEW.task_id;
            ELSEIF row.key = 'time_based_cost' THEN
                -- PROJECT TABLE
                SELECT SUM(t.time_based_cost) FROM task t WHERE t.project_id = projectId INTO totalBasedCostProjectSum;
                UPDATE project SET time_based_cost = totalBasedCostProjectSum WHERE project_id = projectId;
            ELSEIF row.key = 'tm_savings' THEN
                -- PROJECT TABLE
                SELECT SUM(t.tm_savings) FROM task t WHERE t.project_id = projectId INTO totalTmSavingsProjectSum;
                UPDATE project SET tm_savings = totalTmSavingsProjectSum WHERE project_id = projectId;
            ELSEIF row.key = 'minimum' THEN
                IF NEW.minimum = TRUE THEN
                    UPDATE project SET minimum = true WHERE project_id = projectId;
                end if;
            END IF;
        end loop;
    RETURN targetRecord;
END;
$a_task_margin_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION c_task_time_based_cost_func() RETURNS TRIGGER AS
$c_task_time_based_cost_func$
declare
    sumValues    numeric;
    row          record;
    targetRecord record;
BEGIN
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            targetRecord = NEW;

            IF row.key = 'total_cost' THEN
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
                    END)
                           AS total_value
                FROM activity_charge ach
                         INNER JOIN activity a ON ach.activity_id = a.activity_id
                         INNER JOIN calculation_unit cu ON ach.calculation_unit_id = cu.calculation_unit_id
                         INNER JOIN task t ON a.task_id = t.task_id
                WHERE t.task_id = targetRecord.task_id
                  AND cu.type = 'TIME'
                INTO sumValues;
                UPDATE task SET time_based_cost = sumValues WHERE task_id = targetRecord.task_id;
            END IF;
        end loop;
    RETURN targetRecord;
END;
$c_task_time_based_cost_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION tm_saving_items_total_func() RETURNS TRIGGER AS
$tm_saving_items_total_func$
declare
    tmSavingId          bigint;
    taskCatChargeId     bigint;
    sumTotalValue       numeric;
    sumTotalQuantity    numeric;
    totalAmountModifier numeric;
    rateValue           numeric;
    row                 record;
    targetRecord        record;
BEGIN
    SET search_path TO public;
    IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
        tmSavingId = NEW.tm_savings_id;
        targetRecord = NEW;
    ELSE
        tmSavingId = OLD.tm_savings_id;
        targetRecord = OLD;
    END IF;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF row.key = 'fixed_rate' THEN
                SELECT SUM(tmi.fixed_rate * tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = tmSavingId INTO sumTotalValue;
                SELECT total_amount_modifier FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = tmSavingId INTO totalAmountModifier;
                UPDATE task_cat_charge tcch SET total_value = sumTotalValue * totalAmountModifier WHERE tcch.tm_savings_id = tmSavingId;

                SELECT rate FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = tmSavingId INTO rateValue;
                UPDATE task_cat_charge tcch SET weighted_quantity = sumTotalValue / (NULLIF(rateValue, 0)) WHERE tcch.tm_savings_id = tmSavingId;
            ELSEIF row.key = 'quantity' THEN
                SELECT SUM(tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = tmSavingId INTO sumTotalQuantity;
                UPDATE task_cat_charge tcch SET total_quantity = sumTotalQuantity WHERE tcch.tm_savings_id = tmSavingId;
                SELECT task_finance_id FROM task_cat_charge tcch WHERE tcch.tm_savings_id = tmSavingId INTO taskCatChargeId;

                SELECT SUM(tmi.fixed_rate * tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = tmSavingId INTO sumTotalValue;
                SELECT total_amount_modifier FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = tmSavingId INTO totalAmountModifier;
                UPDATE task_cat_charge tcch SET total_value = sumTotalValue * totalAmountModifier WHERE tcch.tm_savings_id = tmSavingId;
                SELECT rate FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = tmSavingId INTO rateValue;
                UPDATE task_cat_charge tcch SET weighted_quantity = sumTotalValue / (NULLIF(rateValue, 0)) WHERE tcch.tm_savings_id = tmSavingId;
            END IF;
        end loop;
    RETURN targetRecord;
END;
$tm_saving_items_total_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION tm_saving_items_total_task_cat_charge_func() RETURNS TRIGGER AS
$tm_saving_items_total_task_cat_charge_func$
declare
    tmSavingId          bigint;
    sumTotalValue       numeric;
    sumTotalQuantity    numeric;
    totalAmountModifier numeric;
    rateValue           numeric;
    targetRecord        record;
BEGIN
    SET search_path TO public;
    tmSavingId = NEW.tm_savings_id;
    targetRecord = NEW;
    SELECT SUM(tmi.fixed_rate * tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = NEW.tm_savings_id INTO sumTotalValue;
    UPDATE task_cat_charge tcch SET total_value = sumTotalValue * NEW.total_amount_modifier WHERE tcch.tm_savings_id = tmSavingId;

    SELECT rate FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = NEW.tm_savings_id INTO rateValue;
    UPDATE task_cat_charge tcch SET weighted_quantity = sumTotalValue / (NULLIF(rateValue, 0)) WHERE tcch.tm_savings_id = NEW.tm_savings_id;

    SELECT SUM(tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = NEW.tm_savings_id INTO sumTotalQuantity;
    UPDATE task_cat_charge tcch SET total_quantity = sumTotalQuantity WHERE tcch.tm_savings_id = NEW.tm_savings_id;

    SELECT SUM(tmi.fixed_rate * tmi.quantity) FROM tm_savings_item tmi WHERE tmi.tm_savings_id = NEW.tm_savings_id INTO sumTotalValue;
    SELECT total_amount_modifier FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = NEW.tm_savings_id INTO totalAmountModifier;
    UPDATE task_cat_charge tcch SET total_value = sumTotalValue * totalAmountModifier WHERE tcch.tm_savings_id = NEW.tm_savings_id;

    SELECT rate FROM task_cat_charge WHERE task_cat_charge.tm_savings_id = NEW.tm_savings_id INTO rateValue;
    UPDATE task_cat_charge tcch SET weighted_quantity = sumTotalValue / (NULLIF(rateValue, 0)) WHERE tcch.tm_savings_id = NEW.tm_savings_id;
    RETURN targetRecord;
END;
$tm_saving_items_total_task_cat_charge_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION tm_saving_items_func() RETURNS TRIGGER AS
$tm_saving_items_func$
declare
    sumTotalValue        numeric;
    taskId               bigint;
    taskFinanceId        bigint;
    totalAmountModifiers numeric;
    row                  record;
    targetRecord         record;
BEGIN
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
                taskFinanceId = NEW.task_finance_id;
                targetRecord = NEW;
            ELSE
                taskFinanceId = OLD.task_finance_id;
                targetRecord = OLD;
            END IF;
            IF row.key = 'total_value' or row.key = 'total_quantity' THEN
                SELECT tf.task_id
                FROM task_finance tf
                         INNER JOIN task t2 on tf.task_finance_id = t2.project_part_finance_id
                WHERE tf.task_finance_id = taskFinanceId
                INTO taskId;
                SELECT tf.total_amount_modifier FROM task_finance tf WHERE tf.task_finance_id = taskFinanceId INTO totalAmountModifiers;

                SELECT SUM(((tcc.total_quantity * tcc.rate - tcc.total_value) * tcc.rate) / (NULLIF(tcc.rate, 0))) FROM task_cat_charge tcc WHERE tcc.task_finance_id = taskFinanceId INTO sumTotalValue;
                UPDATE task t SET tm_savings = sumTotalValue * totalAmountModifiers WHERE t.task_id = taskId;
            END IF;
        end loop;
    RETURN targetRecord;
END;
$tm_saving_items_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION project_func() RETURNS TRIGGER AS
$project_func$
declare
    row         record;
    marginValue numeric;
BEGIN
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF row.key = 'total_cost' or row.key = 'total_agreed' THEN
                marginValue = (1 - NEW.total_cost / (NULLIF(NEW.total_agreed, 0)));
                UPDATE project SET margin = marginValue WHERE project_id = NEW.project_id;
                UPDATE project SET rentability = (marginValue * NEW.total_agreed) / (NULLIF(total_cost, 0)) WHERE project_id = NEW.project_id;
            ELSEIF row.key = 'margin' THEN
                UPDATE project SET rentability = (NEW.margin * NEW.total_agreed) / (NULLIF(total_cost, 0)) WHERE project_id = NEW.project_id;
            END IF;
        end loop;
    RETURN NEW;
END;
$project_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION quote_func() RETURNS TRIGGER AS
$quote_func$
declare
    row         record;
    marginValue numeric;
BEGIN
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF row.key = 'total_cost' or row.key = 'total_agreed' THEN
                marginValue = (1 - NEW.total_cost / (NULLIF(NEW.total_agreed, 0)));
                UPDATE quote SET margin = marginValue WHERE quote_id = NEW.quote_id;
                UPDATE quote SET rentability = (marginValue * NEW.total_agreed) / (NULLIF(total_cost, 0)) WHERE quote_id = NEW.quote_id;
            ELSEIF row.key = 'margin' THEN
                UPDATE quote SET rentability = (NEW.margin * NEW.total_agreed) / (NULLIF(total_cost, 0)) WHERE quote_id = NEW.quote_id;
            END IF;
        end loop;
    RETURN NEW;
END;
$quote_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION job_file_func() RETURNS TRIGGER AS
$job_file_func$
declare
    totalCount numeric;
    taskId     bigint;
BEGIN
    SET search_path TO public;
    IF TG_OP = 'INSERT' THEN
        taskId = NEW.task_id;
    ELSE
        taskId = OLD.task_id;
    END IF;
    SELECT COUNT(*) FROM workflow_job_file wjf WHERE wjf.task_id = taskId AND wjf.category = 'WORKFILE' INTO totalCount;
    UPDATE task SET working_files_number = totalCount WHERE task_id = taskId;
    RETURN NULL;
END;
$job_file_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION activity_close_date_func() RETURNS TRIGGER AS
$activity_close_date_func$
declare
    wfj                 record;
    row                 record;
    targetRecord        record;
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
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
                closeDate = NEW.close_date;
                taskId = NEW.task_id;
                activityId = NEW.activity_id;
                targetRecord = NEW;
                IF TG_OP = 'INSERT' THEN
                    FOR wfj IN SELECT workflow_job.name, workflow_job.workflow_job_id
                               FROM activity
                                        INNER JOIN workflow_job_instance ON activity.workflow_job_instance_id = workflow_job_instance.workflow_job_instance_id
                                        INNER JOIN workflow_job ON workflow_job_instance.workflow_job_id = workflow_job.workflow_job_id
                               WHERE activity_id = activityId
                        LOOP
                            jobName = wfj.name;
                            UPDATE activity SET activity_name = jobName WHERE activity_id = activityId;
                        end loop;
                END IF;
            ELSE
                closeDate = null;
                onTimeStatus = null;
                taskId = OLD.task_id;
                activityId = OLD.activity_id;
                targetRecord = OLD;
            END IF;
            IF row.key = 'close_date' THEN
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
            ELSEIF row.key = 'total_agreed' THEN
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
            ELSEIF row.key = 'status' THEN
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
            END IF;
        end loop;
    RETURN targetRecord;
END;
$activity_close_date_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION total_value_func() RETURNS TRIGGER AS
$total_value_func$
declare
    row           record;
    totalValueSum numeric;
BEGIN
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF row.key = 'quantity' OR row.key = 'rate' OR row.key = 'ignore_minimal_charge' OR row.key = 'minimal_charge' OR row.key = 'total_amount_modifier' THEN
                SELECT (CASE
                            WHEN task_charge.ignore_minimal_charge = true or task_charge.minimal_charge IS NULL THEN
                                    task_charge.quantity * task_charge.rate * task_charge.total_amount_modifier
                            WHEN (task_charge.quantity * task_charge.rate) > task_charge.minimal_charge AND task_charge.ignore_minimal_charge = false AND task_charge.minimal_charge IS NOT NULL THEN
                                    task_charge.quantity * task_charge.rate * task_charge.total_amount_modifier
                            ELSE
                                    task_charge.minimal_charge * task_charge.total_amount_modifier
                    END) AS total_value
                FROM task_charge
                WHERE task_charge_id = NEW.task_charge_id
                INTO totalValueSum;

                UPDATE task_charge SET total_value = totalValueSum WHERE task_charge_id = NEW.task_charge_id;
            END IF;
        end loop;
    RETURN NEW;
END;
$total_value_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION activity_name_func() RETURNS TRIGGER AS
$activity_name_func$
declare
    act        record;
    row        record;
    activityId numeric;
BEGIN
    SET search_path TO public;
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            IF row.key = 'name' THEN
                FOR act IN SELECT activity_id
                           FROM workflow_job
                                    INNER JOIN workflow_job_instance ON workflow_job.workflow_job_id = workflow_job_instance.workflow_job_id
                                    INNER JOIN activity ON workflow_job_instance.workflow_job_instance_id = activity.workflow_job_instance_id
                           WHERE workflow_job.workflow_job_id = NEW.workflow_job_id
                    LOOP
                        activityId = act.activity_id;
                        UPDATE activity SET activity_name = NEW.name WHERE activity_id = activityId;
                    end loop;
            END IF;
        end loop;
    RETURN NEW;
END;
$activity_name_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION customers_create_setup_func() RETURNS TRIGGER AS
$customers_create_setup_func$
declare
    row              record;
    settingId        bigint;
    settingProjectId bigint;
BEGIN
    row = NEW;
    settingId = (select cp_setting_id from cp_setting order by cp_setting_id desc limit 1)+1;
    settingProjectId = (select cp_setting_project_id from cp_setting_project order by cp_setting_project_id desc limit 1)+1;

    INSERT INTO cp_setting_project
    (cp_setting_project_id,
     working_files_as_ref_files,
     update_working_files,
     confirmation_send_by_default,
     download_confirmation,
     deadline_options,
     deadline_options_values,
     specialization_required,
     deep_file_metrics,
     quick_estimate,
     custom_fields)
    VALUES (settingProjectId,
            true,
            true,
            false,
            true,
            'disabled',
            null,
            true,
            false,
            false,
            '[
			  {
				"type": "CHECKBOX",
				"name": "RUSH",
				"key": "rush",
				"value": false
			  },
			  {
				"type": "TEXT",
				"name": "Cost Center - Project ",
				"key": "cost_center",
				"value": ""
			  },
			  {
				"type": "TEXT",
				"name": "OTN Number ",
				"key": "otn_number",
				"value": ""
			  },
			  {
				"type": "TEXT",
				"name": "Billing NUID",
				"key": "nuid",
				"value": ""
			  },
			  {
				"type": "TEXT",
				"name": "Billing Contact ",
				"key": "billing_contact",
				"value": ""
			  },
			  {
				"type": "TEXT",
				"name": "PR- AccStatus",
				"key": "pr_acc_status",
				"value": ""
			  },
			  {
				"type": "TEXT",
				"name": "Purpose",
				"key": "purpose",
				"value": ""
			  },
			  {
				"type": "SELECTION",
				"name": "Domain",
				"key": "domain",
				"value": ""
			  },
			  {
				"type": "SELECTION",
				"name": "Genre",
				"key": "genre",
				"value": ""
			  },
			  {
				"type": "TEXT",
				"name": "Function",
				"key": "function",
				"value": ""
			  },
			  {
				"type": "TEXT",
				"name": "Audience",
				"key": "audience",
				"value": ""
			  },
			  {
				"type": "SELECTION",
				"name": "Template",
				"key": "template",
				"value": null
			  }
			]');
    INSERT INTO cp_setting (cp_setting_id, customer_id, cp_setting_project_id) VALUES (settingId, NEW.customer_id, settingProjectId);
    UPDATE customer
    SET chart_groups = '[
	  "GROUP_DEFAULT"
	]'
    WHERE customer_id = NEW.customer_id;
    RETURN NEW;
END;
$customers_create_setup_func$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION entity_lower_func() RETURNS TRIGGER AS
$entity_lower$
declare
    row          record;
    targetRecord record;
BEGIN
    FOR row IN SELECT (each(hstore(hstore(NEW) - hstore(OLD)))).*
        loop
            targetRecord = NEW;
            IF row.key = 'email' THEN
                UPDATE contact_person SET email = lower(NEW.email) where contact_person_id=NEW.contact_person_id;
            END IF;
        end loop;
    RETURN targetRecord;
END;
$entity_lower$ LANGUAGE plpgsql;
