# AVANTUS

## 1. created a new database
## 2. run schema_avantus.sql
## 3. run ./bin/console doctrine:migrations:migrate
## 4. create publication on the master source database with publications.sql and no_primary_key_tables.sql 
## 5. from the publication drop the following tables:
```
ALTER PUBLICATION avantus
DROP
  TABLE activity, activity_charge, custom_fields, task, tm_savings_item, workflow_job_file, task_finance, task_cat_charge;
```
## 6. create the replication

```
CREATE SUBSCRIPTION avantus_subscription CONNECTION 'dbname=xtrf host=$HOST user=$USER password=$PASSWORD port=25060' PUBLICATION avantus;
```

## 7. Wait for the initial replication to complete
## 8. Create the triggers with custom_fields_triggers.sql
Make sure you have the following extension installed in the database
```
CREATE EXTENSION hstore;
```
## 9. For each of the following tables do:
- add table to the publication
```
ALTER PUBLICATION avantus ADD TABLE $table_name;
```
- refresh the subscription 
```
ALTER SUBSCRIPTION avantus_subscription REFRESH PUBLICATION;
```
- wait for the replication to finish
- repeat with the next table

  ### tables in specific order
  1. task
  2. activity
  3. activity_charge
  4. task_finance
  5. task_cat_charge
  6. tm_savings_item
  7. workflow_job_file
  8. custom_fields

## 10. Run the following queries to syncronized missed triggers

```
select * from syncronize_time_based_cost_func();
select * from syncronize_total_agreed_func();
select * from syncronize_task_total_cost_func();
```