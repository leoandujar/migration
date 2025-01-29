--
-- Name: account; Type: TABLE; Schema: public; Owner: avantdata
--
CREATE TABLE public.account (
  account_id bigint NOT NULL,
  last_modification_date timestamp without time zone,
  version integer NOT NULL,
  active boolean,
  default_entity boolean NOT NULL,
  name character varying(255) NOT NULL,
  prefered_entity boolean NOT NULL,
  abi character varying(255),
  cab character varying(255),
  cin character varying(255),
  account_number character varying(255),
  account_owner_address_address character varying(255),
  account_owner_address_address_2 character varying(255),
  account_owner_address_city character varying(255),
  account_owner_dependent_locality character varying(255),
  account_owner_sorting_code character varying(255),
  account_owner_address_zip_code character varying(255),
  account_owner_name character varying(255) NOT NULL,
  bank_address_address character varying(255),
  bank_address_address_2 character varying(255),
  bank_address_city character varying(255),
  bank_address_dependent_locality character varying(255),
  bank_address_sorting_code character varying(255),
  bank_address_zip_code character varying(255),
  bank_name character varying(255),
  iban_number character varying(255),
  sort_code character varying(255),
  swift character varying(255),
  account_owner_address_country_id bigint,
  account_owner_address_province_id bigint,
  bank_address_country_id bigint,
  bank_address_province_id bigint,
  xtrf_currency_id bigint,
  custom_field_id bigint,
  customer_id bigint,
  payment_method_type_id bigint,
  provider_id bigint,
  intermediary_bank text
);
ALTER TABLE public.account OWNER TO avantdata;
--
  -- Name: account_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.account_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.account_id_sequence OWNER TO avantdata;
--
  -- Name: activity; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.activity (
    activity_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    amount_modifiers character varying(255),
    assign_first_provider boolean,
    auto_split_activity_if_needed boolean,
    auto_total_agreed boolean,
    customer_special_instructions text,
    backup_old_exchange_ratio_not_used numeric(19, 10),
    exchange_ratio_date timestamp without time zone,
    exchange_ratio_event character varying(255),
    ignore_minimal_charge boolean,
    internal_special_instructions text,
    manual_amount_modifier_name text,
    minimal_charge numeric(16, 2),
    payment_note text,
    provider_special_instructions text,
    requests_deadline timestamp without time zone,
    total_agreed numeric(16, 2),
    total_amount_modifier numeric(19, 5),
    words integer,
    auto_calculate_payment_dates boolean,
    bundles_for_output character varying(255) NOT NULL,
    actual_start_date timestamp without time zone,
    close_date timestamp without time zone,
    deadline timestamp without time zone,
    start_date timestamp without time zone NOT NULL,
    directory character varying(2000),
    draft_invoice_date date,
    rating_note double precision NOT NULL,
    files_download_confirmed boolean,
    files_support boolean NOT NULL,
    final_invoice_date date,
    input_files_mode character varying(255),
    internal_feedback text,
    invoice_activity_position integer,
    invoiceable boolean,
    invoiced_in_quote_phase boolean NOT NULL,
    notes_from_provider text,
    order_status character varying(255) NOT NULL,
    payment_date date,
    project_order_recipient_person_type character varying(255),
    project_phase_id_number character varying(255),
    quote_phase_id_number character varying(255),
    status character varying(255) NOT NULL,
    activity_type_id bigint NOT NULL,
    contact_person_id bigint,
    currency_id bigint NOT NULL,
    provider_id bigint,
    provider_price_profile_id bigint,
    workflow_job_instance_id bigint,
    meta_directory_id bigint,
    deadline_reminder_id bigint,
    provider_invoice_id bigint,
    payment_conditions_id bigint,
    task_id bigint NOT NULL,
    template_id bigint,
    vat_rate_id bigint NOT NULL,
    custom_fields_id bigint NOT NULL,
    generate_links_for_external_system boolean DEFAULT true,
    partially_finished boolean DEFAULT false NOT NULL,
    provider_selection_settings_id bigint,
    auction_active boolean NOT NULL,
    job_assignment_id character varying(255),
    half_of_time_reminder_date timestamp without time zone,
    most_of_time_reminder_date timestamp without time zone,
    all_of_time_reminder_date timestamp without time zone,
    job_invoicing_option character varying(255) NOT NULL,
    notes_from_vendor_to_others text,
    visible_in_vp boolean DEFAULT true NOT NULL,
    assigned_person_id bigint
  );
ALTER TABLE public.activity OWNER TO avantdata;
--
  -- Name: activity_amount_modifiers; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.activity_amount_modifiers (
    activity_id bigint NOT NULL,
    amount_modifier_id bigint NOT NULL,
    index integer DEFAULT 0 NOT NULL
  );
ALTER TABLE public.activity_amount_modifiers OWNER TO avantdata;
--
  -- Name: activity_cat_charge; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.activity_cat_charge (
    activity_cat_charge_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    charge_position integer,
    description text,
    ignore_minimal_charge boolean,
    minimal_charge numeric(16, 2),
    rate numeric(19, 5) NOT NULL,
    rate_origin character varying(255) NOT NULL,
    rate_origin_details character varying(255),
    total_amount_modifier numeric(19, 5),
    calculated_in_external_system boolean,
    cat_grid_no_match numeric(19, 4) NOT NULL,
    cat_grid_percent100 numeric(19, 4) NOT NULL,
    cat_grid_percent50_74 numeric(19, 4) NOT NULL,
    cat_grid_percent75_84 numeric(19, 4) NOT NULL,
    cat_grid_percent85_94 numeric(19, 4) NOT NULL,
    cat_grid_percent95_99 numeric(19, 4) NOT NULL,
    cat_grid_repetitions numeric(19, 4) NOT NULL,
    cat_grid_x_translated numeric(19, 4) NOT NULL,
    cat_quantity_no_match numeric(19, 3) NOT NULL,
    cat_quantity_percent100 numeric(19, 3) NOT NULL,
    cat_quantity_percent50_74 numeric(19, 3) NOT NULL,
    cat_quantity_percent75_84 numeric(19, 3) NOT NULL,
    cat_quantity_percent85_94 numeric(19, 3) NOT NULL,
    cat_quantity_percent95_99 numeric(19, 3) NOT NULL,
    cat_quantity_repetitions numeric(19, 3) NOT NULL,
    cat_quantity_x_translated numeric(19, 3) NOT NULL,
    cat_grid_percent100_rate numeric(19, 5),
    cat_grid_percent50_74_rate numeric(19, 5),
    cat_grid_percent75_84_rate numeric(19, 5),
    cat_grid_percent85_94_rate numeric(19, 5),
    cat_grid_percent95_99_rate numeric(19, 5),
    cat_grid_repetitions_rate numeric(19, 5),
    cat_grid_x_translated_rate numeric(19, 5),
    fixed_rate_cat_grid_available boolean,
    metrics_retrieved_from_external_system boolean DEFAULT false NOT NULL,
    calculation_unit_id bigint NOT NULL,
    tm_savings_id bigint NOT NULL,
    activity_id bigint NOT NULL,
    assisted_automated_payable_id text,
    pa_payable_id character varying(255)
  );
ALTER TABLE public.activity_cat_charge OWNER TO avantdata;
--
  -- Name: activity_charge; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.activity_charge (
    activity_charge_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    charge_position integer,
    description text,
    ignore_minimal_charge boolean,
    minimal_charge numeric(16, 2),
    rate numeric(19, 5) NOT NULL,
    rate_origin character varying(255) NOT NULL,
    rate_origin_details character varying(255),
    total_amount_modifier numeric(19, 5),
    percentage_charge_type character varying(255),
    quantity numeric(19, 3) NOT NULL,
    synchronize_with_worklog boolean NOT NULL,
    calculation_unit_id bigint NOT NULL,
    activity_id bigint NOT NULL,
    worklog_autocreated_charge boolean DEFAULT false NOT NULL,
    assisted_automated_payable_id text,
    pa_payable_id character varying(255)
  );
ALTER TABLE public.activity_charge OWNER TO avantdata;
--
  -- Name: activity_charge_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.activity_charge_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.activity_charge_id_sequence OWNER TO avantdata;
--
  -- Name: activity_claim_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.activity_claim_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.activity_claim_id_sequence OWNER TO avantdata;
--
  -- Name: activity_type; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.activity_type (
    activity_type_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    files_needed boolean,
    provided_by_customer boolean DEFAULT false NOT NULL,
    velocity numeric(19, 2),
    relation_to_language character varying(255) NOT NULL,
    velocity_calculation_unit_id bigint,
    localized_entity jsonb
  );
ALTER TABLE public.activity_type OWNER TO avantdata;
--
  -- Name: activity_type_calculation_units; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.activity_type_calculation_units (
    activity_type_id bigint NOT NULL,
    calculation_unit_id bigint NOT NULL
  );
ALTER TABLE public.activity_type_calculation_units OWNER TO avantdata;
--
  -- Name: activity_type_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.activity_type_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.activity_type_id_sequence OWNER TO avantdata;
--
  -- Name: amount_modifier; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.amount_modifier (
    amount_modifier_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    description character varying(255),
    am_type character varying(255) NOT NULL,
    value numeric(19, 4) NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.amount_modifier OWNER TO avantdata;
--
  -- Name: amount_modifier_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.amount_modifier_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.amount_modifier_id_sequence OWNER TO avantdata;
--
  -- Name: authentication_history; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.authentication_history (
    client_ip character varying(255) NOT NULL,
    login character varying(255) NOT NULL,
    user_compound_id character varying(255),
    is_successful boolean NOT NULL,
    login_date timestamp without time zone NOT NULL
  );
ALTER TABLE public.authentication_history OWNER TO avantdata;
--
  -- Name: branch; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.branch (
    branch_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    correspondence_address character varying(255),
    correspondence_address_2 character varying(255),
    correspondence_city character varying(255),
    correspondence_dependent_locality character varying(255),
    correspondence_sorting_code character varying(255),
    correspondence_zipcode character varying(255),
    all_active_payment_methods_available boolean NOT NULL,
    country_specific_fiscal_code_value character varying(255),
    email character varying(255) NOT NULL,
    fax character varying(255),
    fullname character varying(255) NOT NULL,
    headquarters boolean NOT NULL,
    phone character varying(255),
    use_system_default_payment_methods boolean NOT NULL,
    www character varying(255),
    correspondence_country_id bigint,
    correspondence_province_id bigint,
    preferred_currency_id bigint,
    use_default_logo boolean DEFAULT true NOT NULL,
    use_default_home_portal_background boolean DEFAULT true NOT NULL,
    use_default_home_portal_favicon boolean DEFAULT true NOT NULL,
    use_default_vendor_portal_background boolean DEFAULT true NOT NULL,
    use_default_vendor_portal_favicon boolean DEFAULT true NOT NULL,
    use_default_customer_portal_background boolean DEFAULT true NOT NULL,
    use_default_customer_portal_favicon boolean DEFAULT true NOT NULL
  );
ALTER TABLE public.branch OWNER TO avantdata;
--
  -- Name: branch_available_payment_methods; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.branch_available_payment_methods (
    payment_method_id bigint NOT NULL,
    branch_id bigint NOT NULL
  );
ALTER TABLE public.branch_available_payment_methods OWNER TO avantdata;
--
  -- Name: branch_default_payment_methods; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.branch_default_payment_methods (
    payment_method_id bigint NOT NULL,
    branch_id bigint NOT NULL
  );
ALTER TABLE public.branch_default_payment_methods OWNER TO avantdata;
--
  -- Name: branch_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.branch_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.branch_id_sequence OWNER TO avantdata;
--
  -- Name: calculation_unit; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.calculation_unit (
    calculation_unit_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean DEFAULT false NOT NULL,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    conversion_expression text,
    exchange_ratio numeric(19, 10) NOT NULL,
    file_stats_conversion_expression text,
    symbol character varying(255) NOT NULL,
    time_conversion_expression text,
    type character varying(255) NOT NULL,
    use_in_cat_analysis boolean NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.calculation_unit OWNER TO avantdata;
--
  -- Name: calculation_unit_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.calculation_unit_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.calculation_unit_id_sequence OWNER TO avantdata;
--
  -- Name: category; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.category (
    category_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.category OWNER TO avantdata;
--
  -- Name: category_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.category_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.category_id_sequence OWNER TO avantdata;
--
  -- Name: category_supported_classes; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.category_supported_classes (
    category_id bigint NOT NULL,
    supported_class character varying(255) NOT NULL
  );
ALTER TABLE public.category_supported_classes OWNER TO avantdata;
--
  -- Name: charge_definition; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.charge_definition (
    charge_definition_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    date_reference character varying(255),
    days smallint,
    end_of_month boolean,
    months smallint,
    percent_of_invoice_total numeric(8, 6) NOT NULL,
    charge_type_id bigint NOT NULL,
    payment_conditions_id bigint NOT NULL
  );
ALTER TABLE public.charge_definition OWNER TO avantdata;
--
  -- Name: charge_definition_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.charge_definition_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.charge_definition_id_sequence OWNER TO avantdata;
--
  -- Name: charge_type; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.charge_type (
    charge_type_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    fixed boolean,
    localized_entity jsonb
  );
ALTER TABLE public.charge_type OWNER TO avantdata;
--
  -- Name: charge_type_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.charge_type_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.charge_type_id_sequence OWNER TO avantdata;
--
  -- Name: contact_person; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.contact_person (
    contact_person_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    first_contact_date date,
    no_crm_emails boolean,
    notes text,
    use_partner_address_as_address boolean,
    custom_fields_id bigint NOT NULL,
    person_department_id bigint,
    person_position_id bigint,
    email character varying(255),
    address_email2 character varying(255),
    address_email3 character varying(255),
    fax character varying(255),
    mobile_phone character varying(255),
    phone character varying(255),
    address_phone2 character varying(255),
    address_phone3 character varying(255),
    send_cc_to_email_2 boolean,
    send_cc_to_email_3 boolean,
    address_sms_enabled boolean,
    time_zone character varying(255),
    address_www character varying(255),
    address_www2 character varying(255),
    address_address character varying(255),
    address_address_2 character varying(255),
    address_city character varying(255),
    address_dependent_locality character varying(255),
    address_sorting_code character varying(255),
    address_zipcode character varying(255),
    gender character varying(255),
    initials character varying(255),
    last_name character varying(255),
    last_name_normalized character varying(255),
    name character varying(255),
    name_normalized character varying(255),
    system_account_id bigint,
    address_country_id bigint,
    address_province_id bigint,
    preferred_social_media_contact_id bigint,
    social_media_collection_id bigint,
    has_avatar boolean DEFAULT false NOT NULL,
    role character varying(32)
  );
ALTER TABLE public.contact_person OWNER TO avantdata;
--
  -- Name: contact_person_categories2; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.contact_person_categories2 (
    contact_person_id bigint NOT NULL,
    project_category_id bigint NOT NULL
  );
ALTER TABLE public.contact_person_categories2 OWNER TO avantdata;
--
  -- Name: contact_person_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.contact_person_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.contact_person_id_sequence OWNER TO avantdata;
--
  -- Name: country; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.country (
    country_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    country_specific_tax_no1_code_name character varying(255),
    country_specific_tax_no2_code_name character varying(255),
    country_specific_tax_no3_code_name character varying(255),
    fiscal_code_checking_type character varying(255),
    symbol character varying(255) NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.country OWNER TO avantdata;
--
  -- Name: country_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.country_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.country_id_sequence OWNER TO avantdata;
--
  -- Name: custom_field_configuration; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.custom_field_configuration (
    custom_field_configuration_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    default_value text,
    description text,
    key text NOT NULL,
    name text NOT NULL,
    number_precision integer,
    selection_possible_values text,
    type character varying(255) NOT NULL,
    preferences character varying(255) DEFAULT 'READ_WRITE' :: character varying NOT NULL,
    names_in_partner_portals jsonb,
    fields_names jsonb,
    services_option text NOT NULL,
    available_for_qrf boolean NOT NULL,
    available_for_customer_portal boolean DEFAULT false NOT NULL
  );
ALTER TABLE public.custom_field_configuration OWNER TO avantdata;
--
  -- Name: custom_field_configuration_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.custom_field_configuration_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.custom_field_configuration_id_sequence OWNER TO avantdata;
--
  -- Name: custom_fields; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.custom_fields (
    ownertype character varying(31) NOT NULL,
    custom_fields_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    checkbox_field_1 boolean,
    checkbox_field_10 boolean,
    checkbox_field_2 boolean,
    checkbox_field_3 boolean,
    checkbox_field_4 boolean,
    checkbox_field_5 boolean,
    checkbox_field_6 boolean,
    checkbox_field_7 boolean,
    checkbox_field_8 boolean,
    checkbox_field_9 boolean,
    date_field_1 timestamp without time zone,
    date_field_10 timestamp without time zone,
    date_field_2 timestamp without time zone,
    date_field_3 timestamp without time zone,
    date_field_4 timestamp without time zone,
    date_field_5 timestamp without time zone,
    date_field_6 timestamp without time zone,
    date_field_7 timestamp without time zone,
    date_field_8 timestamp without time zone,
    date_field_9 timestamp without time zone,
    number_field_1 numeric(40, 10),
    number_field_10 numeric(40, 10),
    number_field_2 numeric(40, 10),
    number_field_3 numeric(40, 10),
    number_field_4 numeric(40, 10),
    number_field_5 numeric(40, 10),
    number_field_6 numeric(40, 10),
    number_field_7 numeric(40, 10),
    number_field_8 numeric(40, 10),
    number_field_9 numeric(40, 10),
    text_field_1 text,
    text_field_10 text,
    text_field_2 text,
    text_field_3 text,
    text_field_4 text,
    text_field_5 text,
    text_field_6 text,
    text_field_7 text,
    text_field_8 text,
    text_field_9 text,
    select_field_1 text,
    select_field_2 text,
    select_field_3 text,
    select_field_4 text,
    select_field_5 text,
    select_field_6 text,
    select_field_7 text,
    select_field_8 text,
    select_field_9 text,
    select_field_10 text,
    multi_select_field_1 text,
    multi_select_field_2 text,
    multi_select_field_3 text,
    multi_select_field_4 text,
    multi_select_field_5 text,
    multi_select_field_6 text,
    multi_select_field_7 text,
    multi_select_field_8 text,
    multi_select_field_9 text,
    multi_select_field_10 text
  );
ALTER TABLE public.custom_fields OWNER TO avantdata;
--
  -- Name: custom_fields_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.custom_fields_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.custom_fields_id_sequence OWNER TO avantdata;
--
  -- Name: custom_fields_name_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.custom_fields_name_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.custom_fields_name_id_sequence OWNER TO avantdata;
--
  -- Name: customer; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer (
    customer_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    acceptance_of_terms_date timestamp without time zone,
    acceptance_of_terms_type character varying(255),
    address_email character varying(255) NOT NULL,
    address_email2 character varying(255),
    address_email3 character varying(255),
    address_fax character varying(255),
    address_mobile_phone character varying(255),
    address_phone character varying(255),
    address_phone2 character varying(255),
    address_phone3 character varying(255),
    send_cc_to_email_2 boolean,
    send_cc_to_email_3 boolean,
    address_sms_enabled boolean,
    time_zone character varying(255),
    address_www character varying(255),
    address_www2 character varying(255),
    address_address character varying(255),
    address_address_2 character varying(255),
    address_city character varying(255),
    address_dependent_locality character varying(255),
    address_sorting_code character varying(255),
    address_zipcode character varying(255),
    cc_in_emails_to_contact_persons boolean,
    contract_number character varying(255),
    correspondence_address character varying(255),
    correspondence_address_2 character varying(255),
    correspondence_city character varying(255),
    correspondence_dependent_locality character varying(255),
    correspondence_sorting_code character varying(255),
    correspondence_zipcode character varying(255),
    actual_draft_date_reference character varying(255) NOT NULL,
    actual_draft_n_days smallint NOT NULL,
    actual_draft_end_of_month boolean NOT NULL,
    actual_draft_m_months smallint NOT NULL,
    actual_final_date_reference character varying(255) NOT NULL,
    actual_final_n_days smallint NOT NULL,
    actual_final_end_of_month boolean NOT NULL,
    actual_final_m_months smallint NOT NULL,
    expected_draft_date_reference character varying(255) NOT NULL,
    expected_draft_n_days smallint NOT NULL,
    expected_draft_end_of_month boolean NOT NULL,
    expected_draft_m_months smallint NOT NULL,
    expected_final_date_reference character varying(255) NOT NULL,
    expected_final_n_days smallint NOT NULL,
    expected_final_end_of_month boolean NOT NULL,
    expected_final_m_months smallint NOT NULL,
    use_draft boolean,
    first_contact_date date,
    full_name character varying(255) NOT NULL,
    full_name_normalized character varying(255) NOT NULL,
    id_number character varying(255) NOT NULL,
    invoice_note text,
    no_crm_emails boolean,
    notes text,
    sales_notes text,
    single_person boolean,
    tax_no_3 character varying(255),
    use_address_as_correspondence boolean,
    use_default_dates_calculation_rules boolean,
    use_default_user_group boolean NOT NULL,
    account_on_customer_server text,
    create_default_term_if_needed boolean,
    create_default_tm_if_needed boolean,
    enrolment_directory character varying(1800),
    first_last_dates_updated_on timestamp without time zone,
    first_project_date timestamp without time zone,
    first_project_date_auto boolean,
    first_quote_date timestamp without time zone,
    first_quote_date_auto boolean,
    last_project_date timestamp without time zone,
    last_quote_date timestamp without time zone,
    name character varying(255) NOT NULL,
    name_normalized character varying(255) NOT NULL,
    non_payer boolean,
    number_of_projects integer DEFAULT 0 NOT NULL,
    number_of_quotes integer DEFAULT 0 NOT NULL,
    use_default_customer_language_specializations boolean,
    use_default_customer_languages boolean,
    potential_annual_revenue_generation numeric(16, 2),
    potential_annual_revenue_generation_update_date timestamp without time zone,
    send_invoice_email boolean,
    status character varying(255) NOT NULL,
    tax_no_1 character varying(255),
    tax_no_2 character varying(255),
    used_checking_type character varying(255),
    valid_tax_no_1 boolean,
    wire_transfer smallint,
    system_account_id bigint,
    address_country_id bigint,
    address_province_id bigint,
    branch_id bigint NOT NULL,
    correspondence_country_id bigint,
    correspondence_province_id bigint,
    default_payment_conditions_id bigint NOT NULL,
    default_payment_conditions_id_for_empty_invoice bigint NOT NULL,
    lead_source_id bigint,
    preferred_social_media_contact_id bigint,
    standard_property_container_id bigint,
    social_media_collection_id bigint,
    vat_rate_id bigint,
    accountency_contact_person_id bigint,
    custom_fields_id bigint NOT NULL,
    draft_invoice_numbering_schema_id bigint,
    draft_invoice_template_id bigint,
    final_invoice_numbering_schema_id bigint,
    final_invoice_template_id bigint,
    in_house_am_responsible_id bigint,
    in_house_pc_responsible_id bigint,
    in_house_pm_responsible_id bigint NOT NULL,
    in_house_sp_responsible_id bigint NOT NULL,
    linked_provider_id bigint,
    parent_customer_id bigint,
    preferences_id bigint,
    project_confirmation_template_id bigint,
    quote_confirmation_template_id bigint,
    quote_task_confirmation_template_id bigint,
    task_confirmation_template_id bigint,
    xtrf_user_group_id bigint,
    task_files_available_email_template_id bigint,
    customer_portal_price_profile bigint,
    use_default_customer_services boolean DEFAULT true NOT NULL,
    use_default_customer_services_workflows boolean DEFAULT true NOT NULL,
    link_account_identifier text,
    limit_access_to_people_responsible boolean DEFAULT false NOT NULL,
    customer_salesforce_id bigint,
    budget_code_required_when_adding_quote_or_project boolean DEFAULT false NOT NULL,
    has_avatar boolean DEFAULT false NOT NULL
  );
ALTER TABLE public.customer OWNER TO avantdata;
--
  -- Name: customer_accountency_contact_persons; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_accountency_contact_persons (
    customer_id bigint NOT NULL,
    customer_person_id bigint NOT NULL
  );
ALTER TABLE public.customer_accountency_contact_persons OWNER TO avantdata;
--
  -- Name: customer_additional_persons_responsible; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_additional_persons_responsible (
    customer_id bigint NOT NULL,
    xtrf_user_id bigint NOT NULL
  );
ALTER TABLE public.customer_additional_persons_responsible OWNER TO avantdata;
--
  -- Name: customer_categories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_categories (
    customer_id bigint NOT NULL,
    project_category_id bigint NOT NULL
  );
ALTER TABLE public.customer_categories OWNER TO avantdata;
--
  -- Name: customer_charge; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_charge (
    customer_charge_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    due_date date NOT NULL,
    irrecoverable boolean,
    percent_of_total numeric(8, 6),
    value numeric(16, 2) NOT NULL,
    prepayment_clearing_mode character varying(255),
    charge_type_id bigint NOT NULL,
    currency_id bigint NOT NULL,
    customer_id bigint NOT NULL,
    customer_invoice_id bigint,
    project_id bigint
  );
ALTER TABLE public.customer_charge OWNER TO avantdata;
--
  -- Name: customer_charge_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_charge_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_charge_id_sequence OWNER TO avantdata;
--
  -- Name: customer_customer_persons; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_customer_persons (
    person_id bigint NOT NULL,
    customer_id bigint NOT NULL
  );
ALTER TABLE public.customer_customer_persons OWNER TO avantdata;
--
  -- Name: customer_feedback_answer; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_feedback_answer (
    customer_feedback_answer_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    value text,
    project_id bigint NOT NULL,
    customer_feedback_question_id bigint NOT NULL
  );
ALTER TABLE public.customer_feedback_answer OWNER TO avantdata;
--
  -- Name: customer_feedback_answer_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_feedback_answer_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_feedback_answer_id_sequence OWNER TO avantdata;
--
  -- Name: customer_feedback_question; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_feedback_question (
    customer_feedback_question_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    "position" integer,
    type character varying(255) NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.customer_feedback_question OWNER TO avantdata;
--
  -- Name: customer_feedback_question_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_feedback_question_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_feedback_question_id_sequence OWNER TO avantdata;
--
  -- Name: customer_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_id_sequence OWNER TO avantdata;
--
  -- Name: customer_industries; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_industries (
    customer_id bigint NOT NULL,
    industry_id bigint NOT NULL
  );
ALTER TABLE public.customer_industries OWNER TO avantdata;
--
  -- Name: customer_invoice; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_invoice (
    customer_invoice_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    append_currency_to_total_in_words boolean,
    autocalculate_payment_conditions boolean,
    autocalculate_payment_conditions_description boolean,
    draft_date date,
    draft_number character varying(255),
    backup_old_exchange_ratio_not_used numeric(19, 10),
    exchange_ratio_date timestamp without time zone,
    exchange_ratio_event character varying(255),
    final_date date,
    final_number character varying(255),
    fully_paid_date date,
    internal_note text,
    invoice_note text,
    invoice_state_changed_date timestamp without time zone,
    locale character varying(255) NOT NULL,
    paid_value numeric(16, 2) NOT NULL,
    payment_conditions character varying(255),
    payment_note text,
    payment_state character varying(255) NOT NULL,
    pdf_path character varying(2000),
    required_payment_date date,
    total_brutto numeric(16, 2) NOT NULL,
    total_in_words character varying(255),
    total_netto numeric(16, 2) NOT NULL,
    use_converter boolean,
    customer_address character varying(255),
    customer_address_2 character varying(255),
    customer_city character varying(255),
    customer_dependent_locality character varying(255),
    customer_sorting_code character varying(255),
    customer_zip_code character varying(255),
    customer_fiscal_code character varying(255),
    customer_name character varying(255),
    customer_vatue character varying(255),
    draft_number_modified boolean,
    final_number_modified boolean,
    state character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    tasks_id_numbers text,
    tasks_value numeric(16, 2),
    vat_calculation_rule character varying(255) NOT NULL,
    currency_id bigint NOT NULL,
    template_id bigint,
    payment_conditions_id bigint,
    payment_method_id bigint,
    accountency_person_id bigint,
    customer_country_id bigint,
    customer_province_id bigint,
    customer_id bigint,
    customer_bank_account_id bigint,
    numbering_schema_id bigint,
    signed_person_id bigint
  );
ALTER TABLE public.customer_invoice OWNER TO avantdata;
--
  -- Name: customer_invoice_accountency_persons; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_invoice_accountency_persons (
    customer_invoice_id bigint NOT NULL,
    customer_person_id bigint NOT NULL
  );
ALTER TABLE public.customer_invoice_accountency_persons OWNER TO avantdata;
--
  -- Name: customer_invoice_categories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_invoice_categories (
    customer_invoice_id bigint NOT NULL,
    customer_invoice_category_id bigint NOT NULL
  );
ALTER TABLE public.customer_invoice_categories OWNER TO avantdata;
--
  -- Name: customer_invoice_item; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_invoice_item (
    customer_invoice_item_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    amount_modifier numeric(19, 5) NOT NULL,
    item_position integer,
    line_netto numeric(16, 2) NOT NULL,
    name text,
    quantity numeric(19, 3) NOT NULL,
    rate numeric(19, 5) NOT NULL,
    unit text,
    vat_name character varying(255),
    vat_rate numeric(19, 5) NOT NULL,
    customer_invoice_id bigint,
    customer_charge_id bigint,
    vat_id bigint NOT NULL
  );
ALTER TABLE public.customer_invoice_item OWNER TO avantdata;
--
  -- Name: customer_invoice_item_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_invoice_item_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_invoice_item_id_sequence OWNER TO avantdata;
--
  -- Name: customer_language_combination; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_language_combination (
    customer_language_combination_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    source_language_id bigint,
    target_language_id bigint,
    customer_id bigint NOT NULL
  );
ALTER TABLE public.customer_language_combination OWNER TO avantdata;
--
  -- Name: customer_language_combination_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_language_combination_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_language_combination_id_sequence OWNER TO avantdata;
--
  -- Name: customer_language_combination_specializations; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_language_combination_specializations (
    customer_language_combination_id bigint NOT NULL,
    language_specialization_id bigint NOT NULL
  );
ALTER TABLE public.customer_language_combination_specializations OWNER TO avantdata;
--
  -- Name: customer_language_specializations; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_language_specializations (
    customer_id bigint NOT NULL,
    language_specialization_id bigint NOT NULL
  );
ALTER TABLE public.customer_language_specializations OWNER TO avantdata;
--
  -- Name: customer_languages; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_languages (
    customer_id bigint NOT NULL,
    xtrf_language_id bigint NOT NULL
  );
ALTER TABLE public.customer_languages OWNER TO avantdata;
--
  -- Name: customer_minimal_charge; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_minimal_charge (
    customer_minimal_charge_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    rate numeric(16, 2) NOT NULL,
    customer_language_combination_id bigint NOT NULL,
    customer_price_profile_id bigint
  );
ALTER TABLE public.customer_minimal_charge OWNER TO avantdata;
--
  -- Name: customer_minimal_charge_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_minimal_charge_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_minimal_charge_id_sequence OWNER TO avantdata;
--
  -- Name: customer_payment; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_payment (
    customer_payment_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    accepted_value numeric(16, 2) NOT NULL,
    notes text,
    payment_date date,
    received_value numeric(16, 2) NOT NULL,
    accepted_currency_id bigint NOT NULL,
    payment_method_id bigint,
    received_currency_id bigint NOT NULL,
    customer_id bigint NOT NULL,
    prepayment_clearing_correlated_customer_payment_id bigint,
    financial_system_id bigint,
    financial_system_payment_id character varying(255)
  );
ALTER TABLE public.customer_payment OWNER TO avantdata;
--
  -- Name: customer_payment_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_payment_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_payment_id_sequence OWNER TO avantdata;
--
  -- Name: customer_payment_item; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_payment_item (
    customer_payment_item_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    value numeric(16, 2) NOT NULL,
    customer_charge_id bigint NOT NULL,
    customer_payment_id bigint NOT NULL
  );
ALTER TABLE public.customer_payment_item OWNER TO avantdata;
--
  -- Name: customer_payment_item_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_payment_item_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_payment_item_id_sequence OWNER TO avantdata;
--
  -- Name: customer_person; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_person (
    contact_person_id bigint NOT NULL,
    customer_id bigint,
    preferences_id bigint,
    xtrf_user_group_id bigint,
    customer_person_salesforce_id bigint
  );
ALTER TABLE public.customer_person OWNER TO avantdata;
--
  -- Name: customer_price_list; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_price_list (
    customer_price_list_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    currency_id bigint NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.customer_price_list OWNER TO avantdata;
--
  -- Name: customer_price_list_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_price_list_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_price_list_id_sequence OWNER TO avantdata;
--
  -- Name: customer_price_profile; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_price_profile (
    customer_price_profile_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_tm_cat_tool character varying(255),
    default_tm_rates_type character varying(255),
    description character varying(255),
    is_default boolean,
    manual_amount_modifier_name text,
    minimal_charge numeric(16, 2),
    name character varying(255) NOT NULL,
    total_amount_modifier numeric(19, 5),
    default_currency_id bigint NOT NULL,
    customer_id bigint NOT NULL,
    default_contact_person_id bigint,
    price_list_id bigint
  );
ALTER TABLE public.customer_price_profile OWNER TO avantdata;
--
  -- Name: customer_price_profile_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.customer_price_profile_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.customer_price_profile_id_sequence OWNER TO avantdata;
--
  -- Name: customer_services; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.customer_services (
    customer_id bigint NOT NULL,
    service_id bigint NOT NULL,
    workflow_id bigint,
    process_template_id bigint
  );
ALTER TABLE public.customer_services OWNER TO avantdata;
--
  -- Name: external_system_project; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.external_system_project (
    external_system_project_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    project_id character varying(255),
    project_name character varying(4096),
    resources text,
    write_tmp_term character varying(4095),
    write_tmp_tm character varying(4095),
    input_hash text,
    external_system_detailed_status text,
    external_system_status text,
    external_system_status_params text,
    not_owned_by_xtrf boolean,
    mt_engine character varying(4095),
    status character varying(255),
    status_in_progress boolean,
    external_system_id bigint,
    activity_with_all_files_id bigint
  );
ALTER TABLE public.external_system_project OWNER TO avantdata;
--
  -- Name: external_system_project_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.external_system_project_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.external_system_project_id_sequence OWNER TO avantdata;
--
  -- Name: feedback; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.feedback (
    feedback_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    cause_of_discrepancy text,
    feedback_type character varying(255),
    corrective_and_preventive_actions text,
    creation_date timestamp without time zone NOT NULL,
    deadline_for_implementation timestamp without time zone,
    description_of_claim text,
    efficiency_approved_date timestamp without time zone,
    id_number character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    created_by_user_id bigint,
    efficiency_approved_by_user_id bigint,
    related_activity_id bigint,
    template_id bigint,
    related_project_id bigint
  );
ALTER TABLE public.feedback OWNER TO avantdata;
--
  -- Name: feedback_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.feedback_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.feedback_id_sequence OWNER TO avantdata;
--
  -- Name: feedback_related_providers; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.feedback_related_providers (
    feedback_id bigint NOT NULL,
    provider_id bigint NOT NULL
  );
ALTER TABLE public.feedback_related_providers OWNER TO avantdata;
--
  -- Name: feedback_related_tasks; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.feedback_related_tasks (
    feedback_id bigint NOT NULL,
    task_id bigint NOT NULL
  );
ALTER TABLE public.feedback_related_tasks OWNER TO avantdata;
--
  -- Name: feedback_related_users; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.feedback_related_users (
    feedback_id bigint NOT NULL,
    user_id bigint NOT NULL
  );
ALTER TABLE public.feedback_related_users OWNER TO avantdata;
--
  -- Name: feedback_responsible_for_implementation; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.feedback_responsible_for_implementation (
    feedback_id bigint NOT NULL,
    user_id bigint NOT NULL
  );
ALTER TABLE public.feedback_responsible_for_implementation OWNER TO avantdata;
--
  -- Name: file_stats; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.file_stats (
    file_stat_id bigint NOT NULL,
    origin character varying(255) NOT NULL,
    value bigint,
    file_stat_type character varying(255) NOT NULL
  );
ALTER TABLE public.file_stats OWNER TO avantdata;
--
  -- Name: industry; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.industry (
    industry_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.industry OWNER TO avantdata;
--
  -- Name: industry_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.industry_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.industry_id_sequence OWNER TO avantdata;
--
  -- Name: language_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.language_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.language_id_sequence OWNER TO avantdata;
--
  -- Name: language_specialization; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.language_specialization (
    language_specialization_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.language_specialization OWNER TO avantdata;
--
  -- Name: language_specialization_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.language_specialization_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.language_specialization_id_sequence OWNER TO avantdata;
--
  -- Name: lead_source; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.lead_source (
    lead_source_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    available_for_customer boolean,
    available_for_provider boolean,
    localized_entity jsonb
  );
ALTER TABLE public.lead_source OWNER TO avantdata;
--
  -- Name: lead_source_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.lead_source_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.lead_source_id_sequence OWNER TO avantdata;
--
  -- Name: opportunity; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.opportunity (
    opportunity_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    customer_contact_person bigint,
    customer_id bigint,
    sales_person_id bigint,
    expected_close_date timestamp without time zone NOT NULL,
    first_choosed_currency_id bigint,
    name character varying(255) NOT NULL,
    notes text,
    started_on timestamp without time zone NOT NULL,
    pessimistic_amount numeric(19, 2),
    optimistic_amount numeric(19, 2),
    pessimistic_net_amount numeric(19, 2),
    optimistic_net_amount numeric(19, 2),
    optimistic_net_currency_id bigint,
    pessimistic_net_currency_id bigint,
    optimistic_currency_id bigint,
    pessimistic_currency_id bigint,
    optimistic_status_id bigint,
    pessimistic_status_id bigint,
    most_probable_amount numeric(19, 2),
    most_probable_net_amount numeric(19, 2),
    most_probable_currency_id bigint,
    most_probable_net_currency_id bigint,
    most_probable_status_id bigint
  );
ALTER TABLE public.opportunity OWNER TO avantdata;
--
  -- Name: opportunity_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.opportunity_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.opportunity_id_sequence OWNER TO avantdata;
--
  -- Name: opportunity_offer; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.opportunity_offer (
    opportunity_offer_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    close_reason text,
    amount numeric(19, 2),
    currency_id bigint,
    name character varying(255) NOT NULL,
    notes text,
    probability_percent integer,
    quote_id bigint,
    synchronized_with_quote boolean NOT NULL,
    opportunity_id bigint,
    opportunity_status_id bigint NOT NULL,
    close_reason_type_id bigint
  );
ALTER TABLE public.opportunity_offer OWNER TO avantdata;
--
  -- Name: opportunity_offer_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.opportunity_offer_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.opportunity_offer_id_sequence OWNER TO avantdata;
--
  -- Name: opportunity_status; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.opportunity_status (
    opportunity_status_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    created_from_quote_default boolean NOT NULL,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    probability_percent integer,
    prefered_entity boolean NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.opportunity_status OWNER TO avantdata;
--
  -- Name: opportunity_status_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.opportunity_status_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.opportunity_status_id_sequence OWNER TO avantdata;
--
  -- Name: payment_conditions; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.payment_conditions (
    scope character varying(31) NOT NULL,
    payment_conditions_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    id_number character varying(255) NOT NULL,
    gc_system_configuration_id bigint,
    customer_id bigint,
    gp_system_configuration_id bigint,
    provider_id bigint,
    localized_entity jsonb,
    localized_description_expression jsonb
  );
ALTER TABLE public.payment_conditions OWNER TO avantdata;
--
  -- Name: payment_conditions_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.payment_conditions_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.payment_conditions_id_sequence OWNER TO avantdata;
--
  -- Name: person_department; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.person_department (
    person_department_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.person_department OWNER TO avantdata;
--
  -- Name: person_department_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.person_department_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.person_department_id_sequence OWNER TO avantdata;
--
  -- Name: person_native_languages; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.person_native_languages (
    contact_person_id bigint NOT NULL,
    language_id bigint NOT NULL
  );
ALTER TABLE public.person_native_languages OWNER TO avantdata;
--
  -- Name: person_position; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.person_position (
    person_position_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.person_position OWNER TO avantdata;
--
  -- Name: person_position_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.person_position_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.person_position_id_sequence OWNER TO avantdata;
--
  -- Name: previous_activities; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.previous_activities (
    activity_id bigint NOT NULL,
    previous_activity_id bigint NOT NULL
  );
ALTER TABLE public.previous_activities OWNER TO avantdata;
--
  -- Name: project; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.project (
    project_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    customer_project_number character varying(255),
    customer_special_instructions text,
    date_of_event timestamp without time zone,
    id_number character varying(255) NOT NULL,
    internal_special_instructions text,
    name character varying(255),
    notes text,
    order_confirmation_recipient_person_type character varying(255),
    payment_note text,
    place character varying(255),
    project_delivery_method character varying(255),
    project_delivery_settings text,
    provider_special_instructions text,
    quick_note boolean NOT NULL,
    survey_comment text,
    survey_request_date_sent timestamp without time zone,
    survey_sent boolean,
    delivery_date timestamp without time zone,
    actual_start_date timestamp without time zone,
    close_date timestamp without time zone,
    deadline timestamp without time zone,
    start_date timestamp without time zone NOT NULL,
    sent_date timestamp without time zone,
    status character varying(255) NOT NULL,
    account_manager_id bigint,
    customer_contact_person_id bigint,
    currency_id bigint NOT NULL,
    customer_id bigint NOT NULL,
    customer_price_profile_id bigint,
    language_specialization_id bigint,
    project_coordinator_id bigint,
    project_manager_id bigint NOT NULL,
    standard_property_container_id bigint,
    send_back_to_customer_contact_person_id bigint,
    template_id bigint,
    workflow_id bigint,
    account_manager_deadline_reminder_id bigint,
    custom_fields_id bigint NOT NULL,
    project_coordinator_deadline_reminder_id bigint,
    project_manager_deadline_reminder_id bigint,
    quote_id bigint,
    sales_person_id bigint NOT NULL,
    archived_project_file text,
    archived_project_file_password character varying(255),
    created_on timestamp without time zone,
    service_id bigint,
    link_parent_project_id text,
    assisted_project_id character varying(255),
    origin text,
    volume numeric(19, 3),
    budget_code text,
    requested_as_key text,
    archived_at timestamp without time zone
  );
ALTER TABLE public.project OWNER TO avantdata;
--
  -- Name: project_additional_contact_persons; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.project_additional_contact_persons (
    project_id bigint NOT NULL,
    person_id bigint NOT NULL
  );
ALTER TABLE public.project_additional_contact_persons OWNER TO avantdata;
--
  -- Name: project_archived_directories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.project_archived_directories (
    project_id bigint NOT NULL,
    archived_directory text NOT NULL
  );
ALTER TABLE public.project_archived_directories OWNER TO avantdata;
--
  -- Name: project_categories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.project_categories (
    project_id bigint NOT NULL,
    project_category_id bigint NOT NULL
  );
ALTER TABLE public.project_categories OWNER TO avantdata;
--
  -- Name: project_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.project_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.project_id_sequence OWNER TO avantdata;
--
  -- Name: project_language_combination; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.project_language_combination (
    project_id bigint NOT NULL,
    source_language_id bigint,
    target_language_id bigint,
    project_language_combination_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL
  );
ALTER TABLE public.project_language_combination OWNER TO avantdata;
--
  -- Name: project_language_combination_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.project_language_combination_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.project_language_combination_id_sequence OWNER TO avantdata;
--
  -- Name: project_resource; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.project_resource (
    project_resource_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    description text,
    name text,
    resource_url text NOT NULL,
    _default boolean,
    inner_type character varying(255),
    project_resource_type character varying(255),
    resource_type character varying(255),
    customer_id bigint,
    customer_price_profile_id bigint,
    source_language_id bigint,
    target_language_id bigint
  );
ALTER TABLE public.project_resource OWNER TO avantdata;
--
  -- Name: project_resource_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.project_resource_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.project_resource_id_sequence OWNER TO avantdata;
--
  -- Name: provider; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider (
    provider_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    acceptance_of_terms_date timestamp without time zone,
    acceptance_of_terms_type character varying(255),
    address_email character varying(255) NOT NULL,
    address_email2 character varying(255),
    address_email3 character varying(255),
    address_fax character varying(255),
    address_mobile_phone character varying(255),
    address_phone character varying(255),
    address_phone2 character varying(255),
    address_phone3 character varying(255),
    send_cc_to_email_2 boolean,
    send_cc_to_email_3 boolean,
    address_sms_enabled boolean,
    time_zone character varying(255),
    address_www character varying(255),
    address_www2 character varying(255),
    address_address character varying(255),
    address_address_2 character varying(255),
    address_city character varying(255),
    address_dependent_locality character varying(255),
    address_sorting_code character varying(255),
    address_zipcode character varying(255),
    cc_in_emails_to_contact_persons boolean,
    contract_number character varying(255),
    correspondence_address character varying(255),
    correspondence_address_2 character varying(255),
    correspondence_city character varying(255),
    correspondence_dependent_locality character varying(255),
    correspondence_sorting_code character varying(255),
    correspondence_zipcode character varying(255),
    actual_draft_date_reference character varying(255) NOT NULL,
    actual_draft_n_days smallint NOT NULL,
    actual_draft_end_of_month boolean NOT NULL,
    actual_draft_m_months smallint NOT NULL,
    actual_final_date_reference character varying(255) NOT NULL,
    actual_final_n_days smallint NOT NULL,
    actual_final_end_of_month boolean NOT NULL,
    actual_final_m_months smallint NOT NULL,
    expected_draft_date_reference character varying(255) NOT NULL,
    expected_draft_n_days smallint NOT NULL,
    expected_draft_end_of_month boolean NOT NULL,
    expected_draft_m_months smallint NOT NULL,
    expected_final_date_reference character varying(255) NOT NULL,
    expected_final_n_days smallint NOT NULL,
    expected_final_end_of_month boolean NOT NULL,
    expected_final_m_months smallint NOT NULL,
    use_draft boolean,
    first_contact_date date,
    full_name character varying(255) NOT NULL,
    full_name_normalized character varying(255) NOT NULL,
    id_number character varying(255) NOT NULL,
    invoice_note text,
    no_crm_emails boolean,
    notes text,
    sales_notes text,
    single_person boolean,
    tax_no_2 character varying(255),
    use_address_as_correspondence boolean,
    use_default_dates_calculation_rules boolean,
    account_on_provider_server text,
    first_last_dates_updated_on timestamp without time zone,
    first_project_date timestamp without time zone,
    first_project_date_auto boolean,
    in_house boolean NOT NULL,
    invoice_activities boolean,
    last_project_date timestamp without time zone,
    name character varying(255) NOT NULL,
    name_normalized character varying(255) NOT NULL,
    number_of_activities integer DEFAULT 0 NOT NULL,
    enrolment_directory character varying(1800),
    status character varying(255) NOT NULL,
    provider_type character varying(255) NOT NULL,
    system_account_id bigint,
    address_country_id bigint,
    address_province_id bigint,
    branch_id bigint NOT NULL,
    correspondence_country_id bigint,
    correspondence_province_id bigint,
    default_payment_conditions_id bigint NOT NULL,
    default_payment_conditions_id_for_empty_invoice bigint NOT NULL,
    lead_source_id bigint,
    preferred_social_media_contact_id bigint,
    standard_property_container_id bigint,
    social_media_collection_id bigint,
    vat_rate_id bigint,
    accountency_contact_person_id bigint,
    automated_activity_action_id bigint,
    custom_fields_id bigint NOT NULL,
    evaluation_template_id bigint,
    invoice_template_id bigint,
    multiple_purchase_order_template_id bigint,
    preferences_id bigint,
    provider_rating_id bigint,
    purchase_order_template_id bigint,
    provider_experience_id bigint,
    number_of_completed_activities integer DEFAULT 0 NOT NULL,
    number_of_quote_activities integer DEFAULT 0 NOT NULL,
    previous_activity_ready_email_template_id bigint,
    previous_activity_partially_finished_email_template_id bigint,
    link_account_identifier text,
    has_avatar boolean DEFAULT false NOT NULL
  );
ALTER TABLE public.provider OWNER TO avantdata;
--
  -- Name: provider_billing_data; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_billing_data (
    provider_billing_data bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    pesel character varying(255),
    correspondence_address character varying(255),
    correspondence_address_2 character varying(255),
    correspondence_city character varying(255),
    correspondence_dependent_locality character varying(255),
    correspondence_sorting_code character varying(255),
    correspondence_zipcode character varying(255),
    birth_date timestamp without time zone,
    birth_place character varying(255),
    certificate_number character varying(255),
    employed boolean,
    employer_name character varying(255),
    father_name character varying(255),
    mother_maiden_name character varying(255),
    mother_name character varying(255),
    name character varying(255),
    social_security character varying(255),
    special_instructions text,
    tax_no_1 character varying(255),
    type character varying(255),
    correspondence_country_id bigint,
    correspondence_province_id bigint,
    treasury_office_id bigint
  );
ALTER TABLE public.provider_billing_data OWNER TO avantdata;
--
  -- Name: provider_categories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_categories (
    provider_id bigint NOT NULL,
    project_category_id bigint NOT NULL
  );
ALTER TABLE public.provider_categories OWNER TO avantdata;
--
  -- Name: provider_charge; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_charge (
    provider_charge_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    due_date date NOT NULL,
    irrecoverable boolean,
    percent_of_total numeric(8, 6),
    value numeric(16, 2) NOT NULL,
    charge_type_id bigint NOT NULL,
    currency_id bigint NOT NULL,
    provider_invoice_id bigint,
    provider_id bigint NOT NULL
  );
ALTER TABLE public.provider_charge OWNER TO avantdata;
--
  -- Name: provider_charge_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.provider_charge_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.provider_charge_id_sequence OWNER TO avantdata;
--
  -- Name: provider_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.provider_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.provider_id_sequence OWNER TO avantdata;
--
  -- Name: provider_invoice; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_invoice (
    provider_invoice_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    append_currency_to_total_in_words boolean,
    autocalculate_payment_conditions boolean,
    autocalculate_payment_conditions_description boolean,
    draft_date date,
    draft_number character varying(255),
    backup_old_exchange_ratio_not_used numeric(19, 10),
    exchange_ratio_date timestamp without time zone,
    exchange_ratio_event character varying(255),
    final_date date,
    final_number character varying(255),
    fully_paid_date date,
    internal_note text,
    invoice_note text,
    invoice_state_changed_date timestamp without time zone,
    locale character varying(255) NOT NULL,
    paid_value numeric(16, 2) NOT NULL,
    payment_conditions character varying(255),
    payment_note text,
    payment_state character varying(255) NOT NULL,
    pdf_path character varying(2000),
    required_payment_date date,
    total_brutto numeric(16, 2) NOT NULL,
    total_in_words character varying(255),
    total_netto numeric(16, 2) NOT NULL,
    use_converter boolean,
    activities_id_numbers text,
    activities_value numeric(16, 2),
    internal_number character varying(255),
    state character varying(255) NOT NULL,
    notes_from_provider text,
    provider_invoice_file_path text,
    currency_id bigint NOT NULL,
    template_id bigint,
    payment_conditions_id bigint,
    payment_method_id bigint,
    accountency_person_id bigint,
    provider_id bigint,
    specification_date date,
    invoice_upload_date date,
    vat_calculation_rule character varying(255) DEFAULT 'SUM_ITEMS' :: character varying NOT NULL,
    visible_in_vp boolean DEFAULT true NOT NULL
  );
ALTER TABLE public.provider_invoice OWNER TO avantdata;
--
  -- Name: provider_invoice_categories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_invoice_categories (
    provider_invoice_id bigint NOT NULL,
    provider_invoice_category_id bigint NOT NULL
  );
ALTER TABLE public.provider_invoice_categories OWNER TO avantdata;
CREATE SEQUENCE public.provider_invoice_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.provider_invoice_id_sequence OWNER TO avantdata;
--
  -- Name: provider_language_combination; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_language_combination (
    provider_language_combination_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    native_support boolean,
    source_language_id bigint,
    target_language_id bigint,
    provider_id bigint NOT NULL
  );
ALTER TABLE public.provider_language_combination OWNER TO avantdata;
--
  -- Name: provider_language_combination_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.provider_language_combination_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.provider_language_combination_id_sequence OWNER TO avantdata;
--
  -- Name: provider_payment; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_payment (
    provider_payment_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    accepted_value numeric(16, 2) NOT NULL,
    notes text,
    payment_date date,
    received_value numeric(16, 2) NOT NULL,
    accepted_currency_id bigint NOT NULL,
    payment_method_id bigint,
    received_currency_id bigint NOT NULL,
    provider_id bigint NOT NULL,
    financial_system_id bigint,
    financial_system_payment_id character varying(255)
  );
ALTER TABLE public.provider_payment OWNER TO avantdata;
--
  -- Name: provider_payment_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.provider_payment_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.provider_payment_id_sequence OWNER TO avantdata;
--
  -- Name: provider_payment_item; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_payment_item (
    provider_payment_item_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    value numeric(16, 2) NOT NULL,
    provider_charge_id bigint NOT NULL,
    provider_payment_id bigint NOT NULL
  );
ALTER TABLE public.provider_payment_item OWNER TO avantdata;
--
  -- Name: provider_payment_item_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.provider_payment_item_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.provider_payment_item_id_sequence OWNER TO avantdata;
--
  -- Name: provider_person; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.provider_person (
    contact_person_id bigint NOT NULL,
    preferences_id bigint,
    provider_id bigint
  );
ALTER TABLE public.provider_person OWNER TO avantdata;
--
  -- Name: province; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.province (
    province_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    country_id bigint NOT NULL,
    localized_entity jsonb,
    symbol text
  );
ALTER TABLE public.province OWNER TO avantdata;
--
  -- Name: province_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.province_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.province_id_sequence OWNER TO avantdata;
--
  -- Name: quote; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.quote (
    quote_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    customer_project_number character varying(255),
    customer_special_instructions text,
    date_of_event timestamp without time zone,
    id_number character varying(255) NOT NULL,
    internal_special_instructions text,
    name character varying(255),
    notes text,
    order_confirmation_recipient_person_type character varying(255),
    payment_note text,
    place character varying(255),
    project_delivery_method character varying(255),
    project_delivery_settings text,
    provider_special_instructions text,
    quick_note boolean NOT NULL,
    accepter_compound_id character varying(255),
    auto_accept_sent_quote boolean NOT NULL,
    deadline timestamp without time zone,
    start_date timestamp without time zone NOT NULL,
    estimated_delivery_date timestamp without time zone,
    has_associated_offer boolean,
    offer_expiry timestamp without time zone,
    quote_start_date timestamp without time zone,
    rejection_reason text,
    status character varying(255) NOT NULL,
    working_days character varying(255),
    account_manager_id bigint,
    customer_contact_person_id bigint,
    currency_id bigint NOT NULL,
    customer_id bigint NOT NULL,
    customer_price_profile_id bigint,
    language_specialization_id bigint,
    project_coordinator_id bigint,
    project_manager_id bigint NOT NULL,
    standard_property_container_id bigint,
    send_back_to_customer_contact_person_id bigint,
    template_id bigint,
    workflow_id bigint,
    account_manager_expiry_reminder_id bigint,
    custom_fields_id bigint NOT NULL,
    sales_person_id bigint NOT NULL,
    sales_person_expiry_reminder_id bigint,
    service_id bigint,
    link_parent_project_id text,
    assisted_project_id character varying(255),
    origin text,
    rejection_reason_id bigint,
    volume numeric(19, 3),
    budget_code text,
    requested_as_key text,
    archived_quote_file text,
    archived_quote_file_password character varying(255),
    archived_at timestamp without time zone
  );
ALTER TABLE public.quote OWNER TO avantdata;
--
  -- Name: quote_additional_contact_persons; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.quote_additional_contact_persons (
    quote_id bigint NOT NULL,
    person_id bigint NOT NULL
  );
ALTER TABLE public.quote_additional_contact_persons OWNER TO avantdata;
--
  -- Name: quote_categories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.quote_categories (
    quote_id bigint NOT NULL,
    project_category_id bigint NOT NULL
  );
ALTER TABLE public.quote_categories OWNER TO avantdata;
--
  -- Name: quote_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.quote_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.quote_id_sequence OWNER TO avantdata;
--
  -- Name: quote_language_combination; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.quote_language_combination (
    quote_id bigint NOT NULL,
    source_language_id bigint,
    target_language_id bigint,
    quote_language_combination_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL
  );
ALTER TABLE public.quote_language_combination OWNER TO avantdata;
--
  -- Name: quote_language_combination_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.quote_language_combination_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.quote_language_combination_id_sequence OWNER TO avantdata;
--
  -- Name: rejection_reason; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.rejection_reason (
    rejection_reason_id bigint NOT NULL,
    version bigint NOT NULL,
    last_modification_date timestamp without time zone,
    active boolean NOT NULL,
    name text NOT NULL,
    prefered_entity boolean NOT NULL,
    default_entity boolean NOT NULL,
    localized_entity jsonb,
    visible_to_customer boolean NOT NULL
  );
ALTER TABLE public.rejection_reason OWNER TO avantdata;
--
  -- Name: rejection_reason_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.rejection_reason_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.rejection_reason_id_sequence OWNER TO avantdata;
--
  -- Name: service; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.service (
    service_id bigint NOT NULL,
    version bigint NOT NULL,
    last_modification_date timestamp without time zone,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    workflow_id bigint,
    localized_entity jsonb,
    project_type character varying(255) NOT NULL,
    process_template_id bigint,
    custom_field_mappings jsonb,
    activity_type_id bigint
  );
ALTER TABLE public.service OWNER TO avantdata;
--
  -- Name: service_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.service_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.service_id_sequence OWNER TO avantdata;
--
  -- Name: system_account; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.system_account (
    system_account_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    enableflag boolean NOT NULL,
    writepermission boolean NOT NULL,
    homedirectory character varying(255),
    uid character varying(255) NOT NULL,
    userpassword character varying(255),
    shell character varying(255),
    web_login_allowed boolean NOT NULL,
    customer_contact_manage_policy character varying(255) NOT NULL,
    customer_contact_can_accept_and_reject_quote boolean DEFAULT true NOT NULL
  );
ALTER TABLE public.system_account OWNER TO avantdata;
--
  -- Name: system_account_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.system_account_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.system_account_id_sequence OWNER TO avantdata;
--
  -- Name: task; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task (
    task_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    customer_special_instructions text,
    customer_task_number character varying(255),
    date_of_event timestamp without time zone,
    internal_special_instructions text,
    invoiceable boolean,
    name character varying(255),
    notes text,
    payment_note text,
    place character varying(255),
    provider_special_instructions text,
    purpose_and_use_of_translation text,
    quick_note boolean NOT NULL,
    sent_on_date timestamp without time zone,
    style text,
    target_audience text,
    dictionary_directory character varying(2000),
    log_directory character varying(2000),
    ready_directory character varying(2000),
    reference_directory character varying(2000),
    tm_directory character varying(2000),
    workfile_directory character varying(2000),
    order_confirmation_recipient_person_type character varying(255),
    activities_status character varying(255) NOT NULL,
    auto_calculate_payment_dates boolean,
    confirmed_files_downloading boolean,
    delivery_date timestamp without time zone,
    actual_start_date timestamp without time zone,
    close_date timestamp without time zone,
    deadline timestamp without time zone,
    start_date timestamp without time zone NOT NULL,
    draft_invoice_date date,
    final_invoice_date date,
    invoice_task_position integer,
    payment_date date,
    project_phase_id_number character varying(255),
    project_task boolean,
    estimated_delivery_date timestamp without time zone,
    working_days integer,
    quote_phase_id_number character varying(255),
    receivables_status character varying(255) NOT NULL,
    remote_project_id text,
    status character varying(255) NOT NULL,
    customer_contact_person_id bigint,
    external_system_project_id bigint,
    language_specialization_id bigint NOT NULL,
    standard_property_container_id bigint,
    provider_selection_settings_id bigint,
    send_back_to_customer_contact_person_id bigint,
    task_workflow_job_instance_when_embeeded_id bigint,
    vat_rate_id bigint NOT NULL,
    workflow_id bigint,
    workflow_definition_id bigint,
    bundles_meta_directory_when_embeeded_id bigint,
    customer_invoice_id bigint,
    source_language_id bigint,
    target_language_id bigint,
    payment_conditions_id bigint,
    project_id bigint,
    project_coordinator_id bigint,
    project_coordinator_deadline_reminder_id bigint,
    project_manager_id bigint NOT NULL,
    project_manager_deadline_reminder_id bigint,
    project_part_finance_id bigint,
    project_part_template_id bigint,
    quote_id bigint,
    quote_part_finance_id bigint,
    quote_part_template_id bigint,
    link_parent_job_id text,
    link_parent_job_started boolean,
    custom_fields_id bigint NOT NULL,
    activities_total numeric(16, 2),
    CONSTRAINT task_quote_or_project_not_null CHECK (
      (
        (quote_id IS NOT NULL)
        OR (project_id IS NOT NULL)
      )
    )
  );
ALTER TABLE public.task OWNER TO avantdata;
--
  -- Name: task_additional_contact_persons; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_additional_contact_persons (
    task_id bigint NOT NULL,
    person_id bigint NOT NULL
  );
ALTER TABLE public.task_additional_contact_persons OWNER TO avantdata;
--
  -- Name: task_amount_modifiers; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_amount_modifiers (
    task_finance_id bigint NOT NULL,
    amount_modifier_id bigint NOT NULL,
    index integer DEFAULT 0 NOT NULL
  );
ALTER TABLE public.task_amount_modifiers OWNER TO avantdata;
--
  -- Name: task_cat_charge; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_cat_charge (
    task_cat_charge_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    charge_position integer,
    description text,
    ignore_minimal_charge boolean,
    minimal_charge numeric(16, 2),
    rate numeric(19, 5) NOT NULL,
    rate_origin character varying(255) NOT NULL,
    rate_origin_details character varying(255),
    total_amount_modifier numeric(19, 5),
    calculated_in_external_system boolean,
    cat_grid_no_match numeric(19, 4) NOT NULL,
    cat_grid_percent100 numeric(19, 4) NOT NULL,
    cat_grid_percent50_74 numeric(19, 4) NOT NULL,
    cat_grid_percent75_84 numeric(19, 4) NOT NULL,
    cat_grid_percent85_94 numeric(19, 4) NOT NULL,
    cat_grid_percent95_99 numeric(19, 4) NOT NULL,
    cat_grid_repetitions numeric(19, 4) NOT NULL,
    cat_grid_x_translated numeric(19, 4) NOT NULL,
    cat_quantity_no_match numeric(19, 3) NOT NULL,
    cat_quantity_percent100 numeric(19, 3) NOT NULL,
    cat_quantity_percent50_74 numeric(19, 3) NOT NULL,
    cat_quantity_percent75_84 numeric(19, 3) NOT NULL,
    cat_quantity_percent85_94 numeric(19, 3) NOT NULL,
    cat_quantity_percent95_99 numeric(19, 3) NOT NULL,
    cat_quantity_repetitions numeric(19, 3) NOT NULL,
    cat_quantity_x_translated numeric(19, 3) NOT NULL,
    cat_grid_percent100_rate numeric(19, 5),
    cat_grid_percent50_74_rate numeric(19, 5),
    cat_grid_percent75_84_rate numeric(19, 5),
    cat_grid_percent85_94_rate numeric(19, 5),
    cat_grid_percent95_99_rate numeric(19, 5),
    cat_grid_repetitions_rate numeric(19, 5),
    cat_grid_x_translated_rate numeric(19, 5),
    fixed_rate_cat_grid_available boolean,
    creation_date timestamp without time zone,
    input_files text,
    manual_amount_modifier_name text,
    order_confirmation_status character varying(255) NOT NULL,
    status character varying(255),
    calculation_unit_id bigint NOT NULL,
    tm_savings_id bigint NOT NULL,
    activity_type_id bigint NOT NULL,
    task_finance_id bigint NOT NULL,
    assisted_automated_receivable_id text,
    is_or_was_automated boolean,
    pa_receivable_id character varying(255)
  );
ALTER TABLE public.task_cat_charge OWNER TO avantdata;
--
  -- Name: task_cat_charge_amount_modifiers; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_cat_charge_amount_modifiers (
    task_cat_charge_id bigint NOT NULL,
    amount_modifier_id bigint NOT NULL,
    index integer DEFAULT 0 NOT NULL
  );
ALTER TABLE public.task_cat_charge_amount_modifiers OWNER TO avantdata;
--
  -- Name: task_categories; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_categories (
    task_id bigint NOT NULL,
    project_category_id bigint NOT NULL
  );
ALTER TABLE public.task_categories OWNER TO avantdata;
--
  -- Name: task_charge; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_charge (
    task_charge_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    charge_position integer,
    description text,
    ignore_minimal_charge boolean,
    minimal_charge numeric(16, 2),
    rate numeric(19, 5) NOT NULL,
    rate_origin character varying(255) NOT NULL,
    rate_origin_details character varying(255),
    total_amount_modifier numeric(19, 5),
    percentage_charge_type character varying(255),
    quantity numeric(19, 3) NOT NULL,
    manual_amount_modifier_name text,
    order_confirmation_status character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    calculation_unit_id bigint NOT NULL,
    activity_type_id bigint NOT NULL,
    task_finance_id bigint NOT NULL,
    assisted_automated_receivable_id text,
    is_or_was_automated boolean,
    pa_receivable_id character varying(255)
  );
ALTER TABLE public.task_charge OWNER TO avantdata;
--
  -- Name: task_charge_amount_modifiers; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_charge_amount_modifiers (
    task_charge_id bigint NOT NULL,
    amount_modifier_id bigint NOT NULL,
    index integer DEFAULT 0 NOT NULL
  );
ALTER TABLE public.task_charge_amount_modifiers OWNER TO avantdata;
--
  -- Name: task_charge_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.task_charge_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.task_charge_id_sequence OWNER TO avantdata;
--
  -- Name: task_finance; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_finance (
    task_finance_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    amount_modifiers character varying(255),
    auto_total_agreed boolean,
    backup_old_exchange_ratio_not_used numeric(19, 10),
    exchange_ratio_date timestamp without time zone,
    exchange_ratio_event character varying(255),
    ignore_minimal_charge boolean,
    manual_amount_modifier_name text,
    minimal_charge numeric(16, 2),
    total_agreed numeric(16, 2) NOT NULL,
    total_amount_modifier numeric(19, 5),
    task_id bigint,
    currency_id bigint NOT NULL
  );
ALTER TABLE public.task_finance OWNER TO avantdata;
--
  -- Name: task_finance_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.task_finance_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.task_finance_id_sequence OWNER TO avantdata;
--
  -- Name: task_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.task_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.task_id_sequence OWNER TO avantdata;
--
  -- Name: task_workflow_job_instance; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.task_workflow_job_instance (
    task_task_id bigint NOT NULL,
    workflowjobinstances_workflow_job_instance_id bigint NOT NULL
  );
ALTER TABLE public.task_workflow_job_instance OWNER TO avantdata;
--
  -- Name: tm_rates; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.tm_rates (
    discriminator character varying(31) NOT NULL,
    tm_rates_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    cat_tool character varying(255),
    rate_type character varying(255),
    system_configuration_id bigint,
    provider_price_profile_id bigint,
    customer_price_profile_id bigint
  );
ALTER TABLE public.tm_rates OWNER TO avantdata;
--
  -- Name: tm_rates_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.tm_rates_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.tm_rates_id_sequence OWNER TO avantdata;
--
  -- Name: tm_rates_item; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.tm_rates_item (
    tm_rates_id bigint NOT NULL,
    rate numeric(19, 5) NOT NULL,
    match_type character varying(255) NOT NULL
  );
ALTER TABLE public.tm_rates_item OWNER TO avantdata;
--
  -- Name: tm_savings; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.tm_savings (
    tm_savings_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    base_rate numeric(19, 5) NOT NULL,
    cat_tool character varying(255) NOT NULL,
    rates_type character varying(255) NOT NULL,
    cat_analysis text,
    rounding_policy text DEFAULT 'ROUND_LAST' :: text NOT NULL
  );
ALTER TABLE public.tm_savings OWNER TO avantdata;
--
  -- Name: tm_savings_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.tm_savings_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.tm_savings_id_sequence OWNER TO avantdata;
--
  -- Name: tm_savings_item; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.tm_savings_item (
    tm_savings_item_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    fixed_rate numeric(19, 5) NOT NULL,
    match_type character varying(255) NOT NULL,
    percentage_rate numeric(19, 5) NOT NULL,
    quantity numeric(19, 3) NOT NULL,
    tm_savings_id bigint NOT NULL
  );
ALTER TABLE public.tm_savings_item OWNER TO avantdata;
--
  -- Name: tm_savings_item_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.tm_savings_item_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.tm_savings_item_id_sequence OWNER TO avantdata;
--
  -- Name: vat_rate; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.vat_rate (
    vat_rate_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    value numeric(19, 5) NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.vat_rate OWNER TO avantdata;
--
  -- Name: vat_rate_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.vat_rate_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.vat_rate_id_sequence OWNER TO avantdata;
--
  -- Name: workflow; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.workflow (
    workflow_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    auto_convert_quote_accepted_by_customer boolean,
    auto_send_quote_for_customer_confirmation boolean,
    description character varying(255),
    mt_engine character varying(4095),
    is_task_invoiceable boolean,
    default_task_workflow_id bigint,
    workflow_definition_id bigint,
    external_system_id bigint,
    workflow_meta_directories_id bigint,
    standard_property_container_id bigint,
    provider_selection_settings_id bigint,
    allow_customer_to_access_files_in_language_dependent_tasks boolean DEFAULT true NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.workflow OWNER TO avantdata;
--
  -- Name: workflow_job; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.workflow_job (
    jobtype character varying(31) NOT NULL,
    workflow_job_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    all_bundles boolean NOT NULL,
    assign_to_current_pm boolean,
    auto_change_status boolean,
    automatically_assign_all_input_files boolean,
    bundles_for_output character varying(255) NOT NULL,
    copy_missing_output_files_from_input_on_finish boolean,
    default_request_deadline_base character varying(255),
    default_request_deadline_days integer NOT NULL,
    default_request_deadline_hours integer NOT NULL,
    default_request_deadline_use_working boolean NOT NULL,
    estimated_time_weight integer,
    executed_by_external_system boolean,
    external_system_role character varying(255),
    job_invoicing_option character varying(255) NOT NULL,
    job_position integer,
    job_starting_mode character varying(255) NOT NULL,
    mapped_by_external_system_workflow_job boolean,
    name character varying(255) NOT NULL,
    notify_pm_when_activity_partially_finished boolean,
    notify_pm_when_activity_ready boolean,
    notify_provider_when_activity_started boolean,
    payable_quantity numeric(19, 3),
    payables_option character varying(255) NOT NULL,
    show_warning_if_no_out_files boolean,
    bundle_name_expression text,
    activity_type_id bigint,
    bundles_meta_directory_id bigint,
    provider_price_profile_id bigint,
    payable_calculation_unit_id bigint,
    standard_property_container_id bigint,
    provider_selection_rules_id bigint,
    user_defined_activity_partially_finished_email_template_id bigint,
    user_defined_activity_ready_email_template_id bigint,
    workflow_definition_id bigint,
    bundle_schema_id bigint,
    task_workflow_id bigint,
    default_request_deadline_minutes integer DEFAULT 0 NOT NULL,
    automatically_send_po_for_status character varying(255),
    enable_po_download_for_status character varying(255) NOT NULL
  );
ALTER TABLE public.workflow_job OWNER TO avantdata;
--
  -- Name: workflow_job_file; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.workflow_job_file (
    workflow_job_file_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    base_dir text,
    category character varying(255),
    original_file_id character varying(255),
    url character varying(255),
    include_in_availability_request boolean NOT NULL,
    name text,
    provider_time_spent integer,
    relative_dir text,
    resource_id character varying(255),
    resource_type character varying(255),
    activity_id bigint,
    linked_workflow_job_file_id bigint,
    loose_bundle_id bigint,
    external_system_id bigint,
    task_id bigint,
    task_output_id bigint,
    file_stats_status text NOT NULL
  );
ALTER TABLE public.workflow_job_file OWNER TO avantdata;
--
  -- Name: workflow_job_file_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.workflow_job_file_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.workflow_job_file_id_sequence OWNER TO avantdata;
--
  -- Name: workflow_job_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.workflow_job_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.workflow_job_id_sequence OWNER TO avantdata;
--
  -- Name: workflow_job_instance; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.workflow_job_instance (
    jobinsttype character varying(31) NOT NULL,
    workflow_job_instance_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    job_position integer,
    all_bundles_activity_id bigint,
    workflow_job_id bigint,
    all_bundles_task_id bigint,
    project_task_id bigint
  );
ALTER TABLE public.workflow_job_instance OWNER TO avantdata;
--
  -- Name: workflow_job_instance_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.workflow_job_instance_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.workflow_job_instance_id_sequence OWNER TO avantdata;
--
  -- Name: workflow_job_phase; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.workflow_job_phase (
    workflow_job_id bigint NOT NULL,
    phase character varying(255)
  );
ALTER TABLE public.workflow_job_phase OWNER TO avantdata;
--
  -- Name: xtrf_currency; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.xtrf_currency (
    xtrf_currency_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    iso_code character varying(255),
    exchange_ratio numeric(19, 10) NOT NULL,
    symbol character varying(255) NOT NULL,
    localized_entity jsonb
  );
ALTER TABLE public.xtrf_currency OWNER TO avantdata;
--
  -- Name: xtrf_language; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.xtrf_language (
    xtrf_language_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    langiso character varying(255) NOT NULL,
    langiso3 character varying(255),
    symbol character varying(255) NOT NULL,
    localized_entity jsonb,
    multiterm_alias character varying(255)
  );
ALTER TABLE public.xtrf_language OWNER TO avantdata;
--
  -- Name: xtrf_user; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.xtrf_user (
    xtrf_user_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    allocate_licence boolean,
    email character varying(255) NOT NULL,
    xtrf_password character varying(255),
    memoq_password character varying(255),
    expiration_date timestamp without time zone,
    first_name character varying(255) NOT NULL,
    gender character varying(255),
    initials character varying(255),
    last_name character varying(255) NOT NULL,
    xtrf_login character varying(255) NOT NULL,
    mobile_phone character varying(255),
    phone character varying(255),
    sms_enabled boolean,
    time_zone character varying(255),
    branch_id bigint NOT NULL,
    custom_fields_id bigint NOT NULL,
    linked_provider_id bigint,
    person_position_id bigint,
    preferences_id bigint,
    preferred_social_media_contact_id bigint,
    social_media_collection_id bigint,
    xtrf_user_group_id bigint NOT NULL,
    has_legacy_authentication boolean DEFAULT false NOT NULL,
    has_avatar boolean DEFAULT false NOT NULL
  );
ALTER TABLE public.xtrf_user OWNER TO avantdata;
--
  -- Name: xtrf_user_group; Type: TABLE; Schema: public; Owner: avantdata
  --
  CREATE TABLE public.xtrf_user_group (
    xtrf_user_group_id bigint NOT NULL,
    last_modification_date timestamp without time zone,
    version integer NOT NULL,
    active boolean,
    default_entity boolean NOT NULL,
    name character varying(255) NOT NULL,
    prefered_entity boolean NOT NULL,
    customer_status character varying(255),
    person_group boolean NOT NULL,
    usage_group character varying(255) NOT NULL,
    leader_id bigint,
    localized_entity jsonb,
    system_roles character varying(255) []
  );
ALTER TABLE public.xtrf_user_group OWNER TO avantdata;
--
  -- Name: xtrf_user_group_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.xtrf_user_group_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.xtrf_user_group_id_sequence OWNER TO avantdata;
--
  -- Name: xtrf_user_id_sequence; Type: SEQUENCE; Schema: public; Owner: avantdata
  --
  CREATE SEQUENCE public.xtrf_user_id_sequence START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
ALTER TABLE public.xtrf_user_id_sequence OWNER TO avantdata;
--
  -- Name: account account_name_customer_id_provider_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.account
ADD
  CONSTRAINT account_name_customer_id_provider_id_key UNIQUE (name, customer_id, provider_id);
--
  -- Name: account account_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.account
ADD
  CONSTRAINT account_pkey PRIMARY KEY (account_id);
--
  -- Name: activity_amount_modifiers activity_amount_modifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity_amount_modifiers
ADD
  CONSTRAINT activity_amount_modifiers_pkey PRIMARY KEY (activity_id, amount_modifier_id);
--
  -- Name: activity_cat_charge activity_cat_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity_cat_charge
ADD
  CONSTRAINT activity_cat_charge_pkey PRIMARY KEY (activity_cat_charge_id);
--
  -- Name: activity_charge activity_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity_charge
ADD
  CONSTRAINT activity_charge_pkey PRIMARY KEY (activity_charge_id);
--
  -- Name: activity activity_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity
ADD
  CONSTRAINT activity_pkey PRIMARY KEY (activity_id);
--
  -- Name: activity activity_project_phase_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity
ADD
  CONSTRAINT activity_project_phase_id_number_key UNIQUE (project_phase_id_number);
--
  -- Name: activity activity_quote_phase_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity
ADD
  CONSTRAINT activity_quote_phase_id_number_key UNIQUE (quote_phase_id_number);
--
  -- Name: activity_type_calculation_units activity_type_calculation_units_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity_type_calculation_units
ADD
  CONSTRAINT activity_type_calculation_units_pkey PRIMARY KEY (calculation_unit_id, activity_type_id);
--
  -- Name: activity_type activity_type_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity_type
ADD
  CONSTRAINT activity_type_name_key UNIQUE (name);
--
  -- Name: activity_type activity_type_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.activity_type
ADD
  CONSTRAINT activity_type_pkey PRIMARY KEY (activity_type_id);
--
  -- Name: amount_modifier amount_modifier_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.amount_modifier
ADD
  CONSTRAINT amount_modifier_name_key UNIQUE (name);
--
  -- Name: amount_modifier amount_modifier_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.amount_modifier
ADD
  CONSTRAINT amount_modifier_pkey PRIMARY KEY (amount_modifier_id);
--
  -- Name: branch branch_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.branch
ADD
  CONSTRAINT branch_name_key UNIQUE (name);
--
  -- Name: branch branch_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.branch
ADD
  CONSTRAINT branch_pkey PRIMARY KEY (branch_id);
--
  -- Name: calculation_unit calculation_unit_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.calculation_unit
ADD
  CONSTRAINT calculation_unit_name_key UNIQUE (name);
--
  -- Name: calculation_unit calculation_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.calculation_unit
ADD
  CONSTRAINT calculation_unit_pkey PRIMARY KEY (calculation_unit_id);
--
  -- Name: calculation_unit calculation_unit_symbol_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.calculation_unit
ADD
  CONSTRAINT calculation_unit_symbol_key UNIQUE (symbol);
--
  -- Name: category category_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.category
ADD
  CONSTRAINT category_name_key UNIQUE (name);
--
  -- Name: category category_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.category
ADD
  CONSTRAINT category_pkey PRIMARY KEY (category_id);
--
  -- Name: category_supported_classes category_supported_classes_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.category_supported_classes
ADD
  CONSTRAINT category_supported_classes_pkey PRIMARY KEY (category_id, supported_class);
--
  -- Name: charge_type charge_type_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.charge_type
ADD
  CONSTRAINT charge_type_name_key UNIQUE (name);
--
  -- Name: charge_type charge_type_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.charge_type
ADD
  CONSTRAINT charge_type_pkey PRIMARY KEY (charge_type_id);
--
  -- Name: contact_person_categories2 contact_person_categories2_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.contact_person_categories2
ADD
  CONSTRAINT contact_person_categories2_pkey PRIMARY KEY (contact_person_id, project_category_id);
--
  -- Name: contact_person contact_person_custom_fields_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.contact_person
ADD
  CONSTRAINT contact_person_custom_fields_id_key UNIQUE (custom_fields_id);
--
  -- Name: contact_person contact_person_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.contact_person
ADD
  CONSTRAINT contact_person_pkey PRIMARY KEY (contact_person_id);
--
  -- Name: country country_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.country
ADD
  CONSTRAINT country_name_key UNIQUE (name);
--
  -- Name: country country_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.country
ADD
  CONSTRAINT country_pkey PRIMARY KEY (country_id);
--
  -- Name: country country_symbol_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.country
ADD
  CONSTRAINT country_symbol_key UNIQUE (symbol);
--
  -- Name: custom_field_configuration custom_field_configuration_key_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.custom_field_configuration
ADD
  CONSTRAINT custom_field_configuration_key_key UNIQUE (key);
--
  -- Name: custom_field_configuration custom_field_configuration_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.custom_field_configuration
ADD
  CONSTRAINT custom_field_configuration_pkey PRIMARY KEY (custom_field_configuration_id);
--
  -- Name: custom_fields custom_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.custom_fields
ADD
  CONSTRAINT custom_fields_pkey PRIMARY KEY (custom_fields_id);
--
  -- Name: customer_accountency_contact_persons customer_accountency_contact_persons_customer_person_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_accountency_contact_persons
ADD
  CONSTRAINT customer_accountency_contact_persons_customer_person_id_key UNIQUE (customer_person_id);
--
  -- Name: customer_categories customer_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_categories
ADD
  CONSTRAINT customer_categories_pkey PRIMARY KEY (customer_id, project_category_id);
--
  -- Name: customer_charge customer_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_charge
ADD
  CONSTRAINT customer_charge_pkey PRIMARY KEY (customer_charge_id);
--
  -- Name: customer customer_custom_fields_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer
ADD
  CONSTRAINT customer_custom_fields_id_key UNIQUE (custom_fields_id);
--
  -- Name: customer_customer_persons customer_customer_persons_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_customer_persons
ADD
  CONSTRAINT customer_customer_persons_pkey PRIMARY KEY (person_id, customer_id);
--
  -- Name: customer customer_enrolment_directory_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer
ADD
  CONSTRAINT customer_enrolment_directory_key UNIQUE (enrolment_directory);
--
  -- Name: customer_feedback_answer customer_feedback_answer_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_feedback_answer
ADD
  CONSTRAINT customer_feedback_answer_pkey PRIMARY KEY (customer_feedback_answer_id);
--
  -- Name: customer_feedback_question customer_feedback_question_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_feedback_question
ADD
  CONSTRAINT customer_feedback_question_name_key UNIQUE (name);
--
  -- Name: customer_feedback_question customer_feedback_question_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_feedback_question
ADD
  CONSTRAINT customer_feedback_question_pkey PRIMARY KEY (customer_feedback_question_id);
--
  -- Name: customer customer_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer
ADD
  CONSTRAINT customer_id_number_key UNIQUE (id_number);
--
  -- Name: customer_industries customer_industries_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_industries
ADD
  CONSTRAINT customer_industries_pkey PRIMARY KEY (customer_id, industry_id);
--
  -- Name: customer_invoice_categories customer_invoice_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_invoice_categories
ADD
  CONSTRAINT customer_invoice_categories_pkey PRIMARY KEY (
    customer_invoice_id,
    customer_invoice_category_id
  );
--
  -- Name: customer_invoice customer_invoice_draft_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_invoice
ADD
  CONSTRAINT customer_invoice_draft_number_key UNIQUE (draft_number);
--
  -- Name: customer_invoice customer_invoice_final_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_invoice
ADD
  CONSTRAINT customer_invoice_final_number_key UNIQUE (final_number);
--
  -- Name: customer_invoice_item customer_invoice_item_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_invoice_item
ADD
  CONSTRAINT customer_invoice_item_pkey PRIMARY KEY (customer_invoice_item_id);
--
  -- Name: customer_invoice customer_invoice_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_invoice
ADD
  CONSTRAINT customer_invoice_pkey PRIMARY KEY (customer_invoice_id);
--
  -- Name: customer_language_combination customer_language_combination_customer_id_source_language_i_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_language_combination
ADD
  CONSTRAINT customer_language_combination_customer_id_source_language_i_key UNIQUE (
    customer_id,
    source_language_id,
    target_language_id
  );
--
  -- Name: customer_language_combination customer_language_combination_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_language_combination
ADD
  CONSTRAINT customer_language_combination_pkey PRIMARY KEY (customer_language_combination_id);
--
  -- Name: customer_language_combination_specializations customer_language_combination_specializations_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_language_combination_specializations
ADD
  CONSTRAINT customer_language_combination_specializations_pkey PRIMARY KEY (
    customer_language_combination_id,
    language_specialization_id
  );
--
  -- Name: customer_language_specializations customer_language_specializations_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_language_specializations
ADD
  CONSTRAINT customer_language_specializations_pkey PRIMARY KEY (customer_id, language_specialization_id);
--
  -- Name: customer_languages customer_languages_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_languages
ADD
  CONSTRAINT customer_languages_pkey PRIMARY KEY (customer_id, xtrf_language_id);
--
  -- Name: customer_minimal_charge customer_minimal_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_minimal_charge
ADD
  CONSTRAINT customer_minimal_charge_pkey PRIMARY KEY (customer_minimal_charge_id);
--
  -- Name: customer customer_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer
ADD
  CONSTRAINT customer_name_key UNIQUE (name);
--
  -- Name: customer customer_name_normalized_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer
ADD
  CONSTRAINT customer_name_normalized_key UNIQUE (name_normalized);
--
  -- Name: customer_payment_item customer_payment_item_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_payment_item
ADD
  CONSTRAINT customer_payment_item_pkey PRIMARY KEY (customer_payment_item_id);
--
  -- Name: customer_payment customer_payment_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_payment
ADD
  CONSTRAINT customer_payment_pkey PRIMARY KEY (customer_payment_id);
--
  -- Name: customer_person customer_person_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_person
ADD
  CONSTRAINT customer_person_pkey PRIMARY KEY (contact_person_id);
--
  -- Name: customer customer_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer
ADD
  CONSTRAINT customer_pkey PRIMARY KEY (customer_id);
--
  -- Name: customer_price_list customer_price_list_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_price_list
ADD
  CONSTRAINT customer_price_list_name_key UNIQUE (name);
--
  -- Name: customer_price_list customer_price_list_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_price_list
ADD
  CONSTRAINT customer_price_list_pkey PRIMARY KEY (customer_price_list_id);
--
  -- Name: customer_price_profile customer_price_profile_name_customer_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_price_profile
ADD
  CONSTRAINT customer_price_profile_name_customer_id_key UNIQUE (name, customer_id);
--
  -- Name: customer_price_profile customer_price_profile_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_price_profile
ADD
  CONSTRAINT customer_price_profile_pkey PRIMARY KEY (customer_price_profile_id);
--
  -- Name: customer_services customer_services_unique_constraint; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.customer_services
ADD
  CONSTRAINT customer_services_unique_constraint UNIQUE (customer_id, service_id);
--
  -- Name: external_system_project external_system_project_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.external_system_project
ADD
  CONSTRAINT external_system_project_pkey PRIMARY KEY (external_system_project_id);
--
  -- Name: feedback feedback_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.feedback
ADD
  CONSTRAINT feedback_pkey PRIMARY KEY (feedback_id);
--
  -- Name: feedback_related_providers feedback_related_providers_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.feedback_related_providers
ADD
  CONSTRAINT feedback_related_providers_pkey PRIMARY KEY (feedback_id, provider_id);
--
  -- Name: feedback_related_tasks feedback_related_tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.feedback_related_tasks
ADD
  CONSTRAINT feedback_related_tasks_pkey PRIMARY KEY (feedback_id, task_id);
--
  -- Name: feedback_related_users feedback_related_users_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.feedback_related_users
ADD
  CONSTRAINT feedback_related_users_pkey PRIMARY KEY (feedback_id, user_id);
--
  -- Name: feedback_responsible_for_implementation feedback_responsible_for_implementation_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.feedback_responsible_for_implementation
ADD
  CONSTRAINT feedback_responsible_for_implementation_pkey PRIMARY KEY (feedback_id, user_id);
--
  -- Name: file_stats file_stats_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.file_stats
ADD
  CONSTRAINT file_stats_pkey PRIMARY KEY (file_stat_id, file_stat_type);
--
  -- Name: industry industry_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.industry
ADD
  CONSTRAINT industry_name_key UNIQUE (name);
--
  -- Name: industry industry_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.industry
ADD
  CONSTRAINT industry_pkey PRIMARY KEY (industry_id);
--
  -- Name: language_specialization language_specialization_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.language_specialization
ADD
  CONSTRAINT language_specialization_name_key UNIQUE (name);
--
  -- Name: language_specialization language_specialization_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.language_specialization
ADD
  CONSTRAINT language_specialization_pkey PRIMARY KEY (language_specialization_id);
--
  -- Name: lead_source lead_source_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.lead_source
ADD
  CONSTRAINT lead_source_name_key UNIQUE (name);
--
  -- Name: lead_source lead_source_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.lead_source
ADD
  CONSTRAINT lead_source_pkey PRIMARY KEY (lead_source_id);
--
  -- Name: opportunity_offer opportunity_offer_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.opportunity_offer
ADD
  CONSTRAINT opportunity_offer_pkey PRIMARY KEY (opportunity_offer_id);
--
  -- Name: opportunity opportunity_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.opportunity
ADD
  CONSTRAINT opportunity_pkey PRIMARY KEY (opportunity_id);
--
  -- Name: opportunity_status opportunity_status_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.opportunity_status
ADD
  CONSTRAINT opportunity_status_name_key UNIQUE (name);
--
  -- Name: opportunity_status opportunity_status_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.opportunity_status
ADD
  CONSTRAINT opportunity_status_pkey PRIMARY KEY (opportunity_status_id);
--
  -- Name: payment_conditions payment_conditions_name_scope_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.payment_conditions
ADD
  CONSTRAINT payment_conditions_name_scope_id_number_key UNIQUE (name, scope, id_number);
--
  -- Name: payment_conditions payment_conditions_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.payment_conditions
ADD
  CONSTRAINT payment_conditions_pkey PRIMARY KEY (payment_conditions_id);
--
  -- Name: person_department person_department_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.person_department
ADD
  CONSTRAINT person_department_name_key UNIQUE (name);
--
  -- Name: person_department person_department_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.person_department
ADD
  CONSTRAINT person_department_pkey PRIMARY KEY (person_department_id);
--
  -- Name: person_native_languages person_native_languages_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.person_native_languages
ADD
  CONSTRAINT person_native_languages_pkey PRIMARY KEY (contact_person_id, language_id);
--
  -- Name: person_position person_position_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.person_position
ADD
  CONSTRAINT person_position_name_key UNIQUE (name);
--
  -- Name: person_position person_position_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.person_position
ADD
  CONSTRAINT person_position_pkey PRIMARY KEY (person_position_id);
--
  -- Name: previous_activities previous_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.previous_activities
ADD
  CONSTRAINT previous_activities_pkey PRIMARY KEY (activity_id, previous_activity_id);
--
  -- Name: project_additional_contact_persons project_additional_contact_persons_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project_additional_contact_persons
ADD
  CONSTRAINT project_additional_contact_persons_pkey PRIMARY KEY (project_id, person_id);
--
  -- Name: project_archived_directories project_archived_directories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project_archived_directories
ADD
  CONSTRAINT project_archived_directories_pkey PRIMARY KEY (project_id, archived_directory);
--
  -- Name: project_categories project_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project_categories
ADD
  CONSTRAINT project_categories_pkey PRIMARY KEY (project_id, project_category_id);
--
  -- Name: project project_custom_fields_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project
ADD
  CONSTRAINT project_custom_fields_id_key UNIQUE (custom_fields_id);
--
  -- Name: project project_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project
ADD
  CONSTRAINT project_id_number_key UNIQUE (id_number);
--
  -- Name: project_language_combination project_language_combination_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project_language_combination
ADD
  CONSTRAINT project_language_combination_pkey PRIMARY KEY (project_language_combination_id);
--
  -- Name: project project_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project
ADD
  CONSTRAINT project_pkey PRIMARY KEY (project_id);
--
  -- Name: project_language_combination project_unique_combination; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.project_language_combination
ADD
  CONSTRAINT project_unique_combination UNIQUE (
    project_id,
    source_language_id,
    target_language_id
  );
--
  -- Name: provider_billing_data provider_billing_data_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_billing_data
ADD
  CONSTRAINT provider_billing_data_pkey PRIMARY KEY (provider_billing_data);
--
  -- Name: provider_categories provider_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_categories
ADD
  CONSTRAINT provider_categories_pkey PRIMARY KEY (provider_id, project_category_id);
--
  -- Name: provider_charge provider_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_charge
ADD
  CONSTRAINT provider_charge_pkey PRIMARY KEY (provider_charge_id);
--
  -- Name: provider provider_custom_fields_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider
ADD
  CONSTRAINT provider_custom_fields_id_key UNIQUE (custom_fields_id);
--
  -- Name: provider provider_enrolment_directory_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider
ADD
  CONSTRAINT provider_enrolment_directory_key UNIQUE (enrolment_directory);
--
  -- Name: provider provider_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider
ADD
  CONSTRAINT provider_id_number_key UNIQUE (id_number);
--
  -- Name: provider_invoice_categories provider_invoice_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_invoice_categories
ADD
  CONSTRAINT provider_invoice_categories_pkey PRIMARY KEY (
    provider_invoice_id,
    provider_invoice_category_id
  );
--
  -- Name: provider_invoice provider_invoice_internal_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_invoice
ADD
  CONSTRAINT provider_invoice_internal_number_key UNIQUE (internal_number);
--
  -- Name: provider_invoice provider_invoice_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_invoice
ADD
  CONSTRAINT provider_invoice_pkey PRIMARY KEY (provider_invoice_id);
--
  -- Name: provider_language_combination provider_language_combination_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_language_combination
ADD
  CONSTRAINT provider_language_combination_pkey PRIMARY KEY (provider_language_combination_id);
--
  -- Name: provider_language_combination provider_language_combination_provider_id_source_language_i_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_language_combination
ADD
  CONSTRAINT provider_language_combination_provider_id_source_language_i_key UNIQUE (
    provider_id,
    source_language_id,
    target_language_id
  );
--
  -- Name: provider_payment_item provider_payment_item_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_payment_item
ADD
  CONSTRAINT provider_payment_item_pkey PRIMARY KEY (provider_payment_item_id);
--
  -- Name: provider_payment provider_payment_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_payment
ADD
  CONSTRAINT provider_payment_pkey PRIMARY KEY (provider_payment_id);
--
  -- Name: provider_person provider_person_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider_person
ADD
  CONSTRAINT provider_person_pkey PRIMARY KEY (contact_person_id);
--
  -- Name: provider provider_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.provider
ADD
  CONSTRAINT provider_pkey PRIMARY KEY (provider_id);
--
  -- Name: province province_name_country_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.province
ADD
  CONSTRAINT province_name_country_id_key UNIQUE (name, country_id);
--
  -- Name: province province_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.province
ADD
  CONSTRAINT province_pkey PRIMARY KEY (province_id);
--
  -- Name: quote_additional_contact_persons quote_additional_contact_persons_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.quote_additional_contact_persons
ADD
  CONSTRAINT quote_additional_contact_persons_pkey PRIMARY KEY (quote_id, person_id);
--
  -- Name: quote_categories quote_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.quote_categories
ADD
  CONSTRAINT quote_categories_pkey PRIMARY KEY (quote_id, project_category_id);
--
  -- Name: quote quote_custom_fields_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.quote
ADD
  CONSTRAINT quote_custom_fields_id_key UNIQUE (custom_fields_id);
--
  -- Name: quote quote_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.quote
ADD
  CONSTRAINT quote_id_number_key UNIQUE (id_number);
--
  -- Name: quote_language_combination quote_language_combination_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.quote_language_combination
ADD
  CONSTRAINT quote_language_combination_pkey PRIMARY KEY (quote_language_combination_id);
--
  -- Name: quote quote_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.quote
ADD
  CONSTRAINT quote_pkey PRIMARY KEY (quote_id);
--
  -- Name: quote_language_combination quote_unique_combination; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.quote_language_combination
ADD
  CONSTRAINT quote_unique_combination UNIQUE (quote_id, source_language_id, target_language_id);
--
  -- Name: rejection_reason rejection_reason_name_unique; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.rejection_reason
ADD
  CONSTRAINT rejection_reason_name_unique UNIQUE (name);
--
  -- Name: rejection_reason rejection_reason_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.rejection_reason
ADD
  CONSTRAINT rejection_reason_pkey PRIMARY KEY (rejection_reason_id);
--
  -- Name: service service_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.service
ADD
  CONSTRAINT service_name_key UNIQUE (name);
--
  -- Name: service service_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.service
ADD
  CONSTRAINT service_pkey PRIMARY KEY (service_id);
--
  -- Name: system_account system_account_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.system_account
ADD
  CONSTRAINT system_account_pkey PRIMARY KEY (system_account_id);
--
  -- Name: system_account system_account_uid_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.system_account
ADD
  CONSTRAINT system_account_uid_key UNIQUE (uid);
--
  -- Name: task_amount_modifiers task_amount_modifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_amount_modifiers
ADD
  CONSTRAINT task_amount_modifiers_pkey PRIMARY KEY (task_finance_id, amount_modifier_id);
--
  -- Name: task_cat_charge_amount_modifiers task_cat_charge_amount_modifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_cat_charge_amount_modifiers
ADD
  CONSTRAINT task_cat_charge_amount_modifiers_pkey PRIMARY KEY (task_cat_charge_id, amount_modifier_id);
--
  -- Name: task_cat_charge task_cat_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_cat_charge
ADD
  CONSTRAINT task_cat_charge_pkey PRIMARY KEY (task_cat_charge_id);
--
  -- Name: task_categories task_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_categories
ADD
  CONSTRAINT task_categories_pkey PRIMARY KEY (task_id, project_category_id);
--
  -- Name: task_charge_amount_modifiers task_charge_amount_modifiers_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_charge_amount_modifiers
ADD
  CONSTRAINT task_charge_amount_modifiers_pkey PRIMARY KEY (task_charge_id, amount_modifier_id);
--
  -- Name: task_charge task_charge_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_charge
ADD
  CONSTRAINT task_charge_pkey PRIMARY KEY (task_charge_id);
--
  -- Name: task_finance task_finance_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_finance
ADD
  CONSTRAINT task_finance_pkey PRIMARY KEY (task_finance_id);
--
  -- Name: task task_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task
ADD
  CONSTRAINT task_pkey PRIMARY KEY (task_id);
--
  -- Name: task task_project_phase_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task
ADD
  CONSTRAINT task_project_phase_id_number_key UNIQUE (project_phase_id_number);
--
  -- Name: task task_quote_phase_id_number_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task
ADD
  CONSTRAINT task_quote_phase_id_number_key UNIQUE (quote_phase_id_number);
--
  -- Name: task_workflow_job_instance task_workflow_job_instance_workflowjobinstances_workflow_jo_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.task_workflow_job_instance
ADD
  CONSTRAINT task_workflow_job_instance_workflowjobinstances_workflow_jo_key UNIQUE (workflowjobinstances_workflow_job_instance_id);
--
  -- Name: tm_rates_item tm_rates_item_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.tm_rates_item
ADD
  CONSTRAINT tm_rates_item_pkey PRIMARY KEY (tm_rates_id, match_type);
--
  -- Name: tm_rates tm_rates_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.tm_rates
ADD
  CONSTRAINT tm_rates_pkey PRIMARY KEY (tm_rates_id);
--
  -- Name: tm_savings_item tm_savings_item_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.tm_savings_item
ADD
  CONSTRAINT tm_savings_item_pkey PRIMARY KEY (tm_savings_item_id);
--
  -- Name: tm_savings tm_savings_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.tm_savings
ADD
  CONSTRAINT tm_savings_pkey PRIMARY KEY (tm_savings_id);
--
  -- Name: vat_rate vat_rate_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.vat_rate
ADD
  CONSTRAINT vat_rate_name_key UNIQUE (name);
--
  -- Name: vat_rate vat_rate_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.vat_rate
ADD
  CONSTRAINT vat_rate_pkey PRIMARY KEY (vat_rate_id);
--
  -- Name: workflow_job_file workflow_job_file_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.workflow_job_file
ADD
  CONSTRAINT workflow_job_file_pkey PRIMARY KEY (workflow_job_file_id);
--
  -- Name: workflow_job_instance workflow_job_instance_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.workflow_job_instance
ADD
  CONSTRAINT workflow_job_instance_pkey PRIMARY KEY (workflow_job_instance_id);
--
  -- Name: workflow_job workflow_job_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.workflow_job
ADD
  CONSTRAINT workflow_job_pkey PRIMARY KEY (workflow_job_id);
--
  -- Name: workflow workflow_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.workflow
ADD
  CONSTRAINT workflow_name_key UNIQUE (name);
--
  -- Name: workflow workflow_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.workflow
ADD
  CONSTRAINT workflow_pkey PRIMARY KEY (workflow_id);
--
  -- Name: xtrf_currency xtrf_currency_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_currency
ADD
  CONSTRAINT xtrf_currency_name_key UNIQUE (name);
--
  -- Name: xtrf_currency xtrf_currency_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_currency
ADD
  CONSTRAINT xtrf_currency_pkey PRIMARY KEY (xtrf_currency_id);
--
  -- Name: xtrf_language xtrf_language_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_language
ADD
  CONSTRAINT xtrf_language_name_key UNIQUE (name);
--
  -- Name: xtrf_language xtrf_language_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_language
ADD
  CONSTRAINT xtrf_language_pkey PRIMARY KEY (xtrf_language_id);
--
  -- Name: xtrf_user xtrf_user_custom_fields_id_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_user
ADD
  CONSTRAINT xtrf_user_custom_fields_id_key UNIQUE (custom_fields_id);
--
  -- Name: xtrf_user_group xtrf_user_group_customer_status_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_user_group
ADD
  CONSTRAINT xtrf_user_group_customer_status_key UNIQUE (customer_status);
--
  -- Name: xtrf_user_group xtrf_user_group_name_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_user_group
ADD
  CONSTRAINT xtrf_user_group_name_key UNIQUE (name);
--
  -- Name: xtrf_user_group xtrf_user_group_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_user_group
ADD
  CONSTRAINT xtrf_user_group_pkey PRIMARY KEY (xtrf_user_group_id);
--
  -- Name: xtrf_user xtrf_user_pkey; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_user
ADD
  CONSTRAINT xtrf_user_pkey PRIMARY KEY (xtrf_user_id);
--
  -- Name: xtrf_user xtrf_user_xtrf_login_key; Type: CONSTRAINT; Schema: public; Owner: avantdata
  --
ALTER TABLE ONLY public.xtrf_user
ADD
  CONSTRAINT xtrf_user_xtrf_login_key UNIQUE (xtrf_login);
--
  -- Name: account_account_owner_address_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX account_account_owner_address_country_id_idx ON public.account USING btree (account_owner_address_country_id);
--
  -- Name: account_account_owner_address_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX account_account_owner_address_province_id_idx ON public.account USING btree (account_owner_address_province_id);
--
  -- Name: account_custom_field_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX account_custom_field_id_idx ON public.account USING btree (custom_field_id);
--
  -- Name: account_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX account_customer_id_idx ON public.account USING btree (customer_id);
--
  -- Name: account_payment_method_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX account_payment_method_type_id_idx ON public.account USING btree (payment_method_type_id);
--
  -- Name: account_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX account_provider_id_idx ON public.account USING btree (provider_id);
--
  -- Name: account_xtrf_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX account_xtrf_currency_id_idx ON public.account USING btree (xtrf_currency_id);
--
  -- Name: activity_activity_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_activity_type_id_idx ON public.activity USING btree (activity_type_id);
--
  -- Name: activity_amount_modifiers_activity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_amount_modifiers_activity_id_idx ON public.activity_amount_modifiers USING btree (activity_id);
--
  -- Name: activity_amount_modifiers_amount_modifier_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_amount_modifiers_amount_modifier_id_idx ON public.activity_amount_modifiers USING btree (amount_modifier_id);
--
  -- Name: activity_auction_active_status_request_deadline_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_auction_active_status_request_deadline_idx ON public.activity USING btree (auction_active, status, requests_deadline);
--
  -- Name: activity_cat_charge_activity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_cat_charge_activity_id_idx ON public.activity_cat_charge USING btree (activity_id);
--
  -- Name: activity_cat_charge_assisted_automated_payable_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_cat_charge_assisted_automated_payable_id_idx ON public.activity_cat_charge USING btree (assisted_automated_payable_id);
--
  -- Name: activity_cat_charge_calculation_unit_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_cat_charge_calculation_unit_id_idx ON public.activity_cat_charge USING btree (calculation_unit_id);
--
  -- Name: activity_cat_charge_tm_savings_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_cat_charge_tm_savings_id_idx ON public.activity_cat_charge USING btree (tm_savings_id);
--
  -- Name: activity_charge_activity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_charge_activity_id_idx ON public.activity_charge USING btree (activity_id);
--
  -- Name: activity_charge_assisted_automated_payable_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_charge_assisted_automated_payable_id_idx ON public.activity_charge USING btree (assisted_automated_payable_id);
--
  -- Name: activity_charge_calculation_unit_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_charge_calculation_unit_id_idx ON public.activity_charge USING btree (calculation_unit_id);
--
  -- Name: activity_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_contact_person_id_idx ON public.activity USING btree (contact_person_id);
--
  -- Name: activity_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_currency_id_idx ON public.activity USING btree (currency_id);
--
  -- Name: activity_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_custom_fields_id_idx ON public.activity USING btree (custom_fields_id);
--
  -- Name: activity_job_assignment_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_job_assignment_id_idx ON public.activity USING btree (job_assignment_id);
--
  -- Name: activity_meta_directory_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_meta_directory_id_idx ON public.activity USING btree (meta_directory_id);
--
  -- Name: activity_payment_conditions_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_payment_conditions_id_idx ON public.activity USING btree (payment_conditions_id);
--
  -- Name: activity_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_provider_id_idx ON public.activity USING btree (provider_id);
--
  -- Name: activity_provider_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_provider_invoice_id_idx ON public.activity USING btree (provider_invoice_id);
--
  -- Name: activity_provider_price_profile_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_provider_price_profile_id_idx ON public.activity USING btree (provider_price_profile_id);
--
  -- Name: activity_provider_selection_settings_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_provider_selection_settings_id_idx ON public.activity USING btree (provider_selection_settings_id);
--
  -- Name: activity_task_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_task_id ON public.activity USING btree (task_id);
--
  -- Name: activity_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_task_id_idx ON public.activity USING btree (task_id);
--
  -- Name: activity_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_template_id_idx ON public.activity USING btree (template_id);
--
  -- Name: activity_type_calculation_units_activity_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_type_calculation_units_activity_type_id_idx ON public.activity_type_calculation_units USING btree (activity_type_id);
--
  -- Name: activity_type_calculation_units_calculation_unit_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_type_calculation_units_calculation_unit_id_idx ON public.activity_type_calculation_units USING btree (calculation_unit_id);
--
  -- Name: activity_type_velocity_calculation_unit_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_type_velocity_calculation_unit_id_idx ON public.activity_type USING btree (velocity_calculation_unit_id);
--
  -- Name: activity_vat_rate_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_vat_rate_id_idx ON public.activity USING btree (vat_rate_id);
--
  -- Name: activity_workflow_job_instance_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX activity_workflow_job_instance_id_idx ON public.activity USING btree (workflow_job_instance_id);
--
  -- Name: authentication_history_is_successful_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX authentication_history_is_successful_idx ON public.authentication_history USING btree (is_successful);
--
  -- Name: authentication_history_login_date_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX authentication_history_login_date_idx ON public.authentication_history USING btree (login_date);
--
  -- Name: branch_correspondence_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX branch_correspondence_country_id_idx ON public.branch USING btree (correspondence_country_id);
--
  -- Name: branch_correspondence_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX branch_correspondence_province_id_idx ON public.branch USING btree (correspondence_province_id);
--
  -- Name: branch_preferred_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX branch_preferred_currency_id_idx ON public.branch USING btree (preferred_currency_id);
--
  -- Name: category_supported_classes_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX category_supported_classes_category_id_idx ON public.category_supported_classes USING btree (category_id);
--
  -- Name: charge_definition_charge_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX charge_definition_charge_type_id_idx ON public.charge_definition USING btree (charge_type_id);
--
  -- Name: contact_person_address_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_address_country_id_idx ON public.contact_person USING btree (address_country_id);
--
  -- Name: contact_person_address_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_address_province_id_idx ON public.contact_person USING btree (address_province_id);
--
  -- Name: contact_person_categories2_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_categories2_contact_person_id_idx ON public.contact_person_categories2 USING btree (contact_person_id);
--
  -- Name: contact_person_categories2_project_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_categories2_project_category_id_idx ON public.contact_person_categories2 USING btree (project_category_id);
--
  -- Name: contact_person_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_custom_fields_id_idx ON public.contact_person USING btree (custom_fields_id);
--
  -- Name: contact_person_person_department_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_person_department_id_idx ON public.contact_person USING btree (person_department_id);
--
  -- Name: contact_person_person_position_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_person_position_id_idx ON public.contact_person USING btree (person_position_id);
--
  -- Name: contact_person_preferred_social_media_contact_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_preferred_social_media_contact_id_idx ON public.contact_person USING btree (preferred_social_media_contact_id);
--
  -- Name: contact_person_social_media_collection_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_social_media_collection_id_idx ON public.contact_person USING btree (social_media_collection_id);
--
  -- Name: contact_person_system_account_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX contact_person_system_account_id_idx ON public.contact_person USING btree (system_account_id);
--
  -- Name: customer_accountency_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_accountency_contact_person_id_idx ON public.customer USING btree (accountency_contact_person_id);
--
  -- Name: customer_accountency_contact_persons_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_accountency_contact_persons_customer_id_idx ON public.customer_accountency_contact_persons USING btree (customer_id);
--
  -- Name: customer_accountency_contact_persons_customer_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_accountency_contact_persons_customer_person_id_idx ON public.customer_accountency_contact_persons USING btree (customer_person_id);
--
  -- Name: customer_additional_persons_responsible_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_additional_persons_responsible_customer_id_idx ON public.customer_additional_persons_responsible USING btree (customer_id);
--
  -- Name: customer_additional_persons_responsible_xtrf_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_additional_persons_responsible_xtrf_user_id_idx ON public.customer_additional_persons_responsible USING btree (xtrf_user_id);
--
  -- Name: customer_address_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_address_country_id_idx ON public.customer USING btree (address_country_id);
--
  -- Name: customer_address_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_address_province_id_idx ON public.customer USING btree (address_province_id);
--
  -- Name: customer_branch_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_branch_id_idx ON public.customer USING btree (branch_id);
--
  -- Name: customer_categories_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_categories_customer_id_idx ON public.customer_categories USING btree (customer_id);
--
  -- Name: customer_categories_project_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_categories_project_category_id_idx ON public.customer_categories USING btree (project_category_id);
--
  -- Name: customer_charge_charge_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_charge_charge_type_id_idx ON public.customer_charge USING btree (charge_type_id);
--
  -- Name: customer_charge_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_charge_currency_id_idx ON public.customer_charge USING btree (currency_id);
--
  -- Name: customer_charge_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_charge_customer_id_idx ON public.customer_charge USING btree (customer_id);
--
  -- Name: customer_charge_customer_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_charge_customer_invoice_id_idx ON public.customer_charge USING btree (customer_invoice_id);
--
  -- Name: customer_charge_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_charge_project_id_idx ON public.customer_charge USING btree (project_id);
--
  -- Name: customer_correspondence_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_correspondence_country_id_idx ON public.customer USING btree (correspondence_country_id);
--
  -- Name: customer_correspondence_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_correspondence_province_id_idx ON public.customer USING btree (correspondence_province_id);
--
  -- Name: customer_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_custom_fields_id_idx ON public.customer USING btree (custom_fields_id);
--
  -- Name: customer_customer_persons_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_customer_persons_customer_id_idx ON public.customer_customer_persons USING btree (customer_id);
--
  -- Name: customer_customer_persons_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_customer_persons_person_id_idx ON public.customer_customer_persons USING btree (person_id);
--
  -- Name: customer_customer_portal_price_profile_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_customer_portal_price_profile_idx ON public.customer USING btree (customer_portal_price_profile);
--
  -- Name: customer_customer_salesforce_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_customer_salesforce_id_idx ON public.customer USING btree (customer_salesforce_id);
--
  -- Name: customer_default_payment_conditions_id_for_empty_invoice_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_default_payment_conditions_id_for_empty_invoice_idx ON public.customer USING btree (default_payment_conditions_id_for_empty_invoice);
--
  -- Name: customer_default_payment_conditions_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_default_payment_conditions_id_idx ON public.customer USING btree (default_payment_conditions_id);
--
  -- Name: customer_draft_invoice_numbering_schema_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_draft_invoice_numbering_schema_id_idx ON public.customer USING btree (draft_invoice_numbering_schema_id);
--
  -- Name: customer_draft_invoice_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_draft_invoice_template_id_idx ON public.customer USING btree (draft_invoice_template_id);
--
  -- Name: customer_feedback_answer_customer_feedback_question_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_feedback_answer_customer_feedback_question_id_idx ON public.customer_feedback_answer USING btree (customer_feedback_question_id);
--
  -- Name: customer_feedback_answer_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_feedback_answer_project_id_idx ON public.customer_feedback_answer USING btree (project_id);
--
  -- Name: customer_final_invoice_numbering_schema_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_final_invoice_numbering_schema_id_idx ON public.customer USING btree (final_invoice_numbering_schema_id);
--
  -- Name: customer_final_invoice_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_final_invoice_template_id_idx ON public.customer USING btree (final_invoice_template_id);
--
  -- Name: customer_in_house_am_responsible_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_in_house_am_responsible_id_idx ON public.customer USING btree (in_house_am_responsible_id);
--
  -- Name: customer_in_house_pc_responsible_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_in_house_pc_responsible_id_idx ON public.customer USING btree (in_house_pc_responsible_id);
--
  -- Name: customer_in_house_pm_responsible_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_in_house_pm_responsible_id_idx ON public.customer USING btree (in_house_pm_responsible_id);
--
  -- Name: customer_in_house_sp_responsible_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_in_house_sp_responsible_id_idx ON public.customer USING btree (in_house_sp_responsible_id);
--
  -- Name: customer_industries_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_industries_customer_id_idx ON public.customer_industries USING btree (customer_id);
--
  -- Name: customer_industries_industry_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_industries_industry_id_idx ON public.customer_industries USING btree (industry_id);
--
  -- Name: customer_invoice_accountency_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_accountency_person_id_idx ON public.customer_invoice USING btree (accountency_person_id);
--
  -- Name: customer_invoice_accountency_persons_customer_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_accountency_persons_customer_invoice_id_idx ON public.customer_invoice_accountency_persons USING btree (customer_invoice_id);
--
  -- Name: customer_invoice_accountency_persons_customer_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_accountency_persons_customer_person_id_idx ON public.customer_invoice_accountency_persons USING btree (customer_person_id);
--
  -- Name: customer_invoice_categories_customer_invoice_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_categories_customer_invoice_category_id_idx ON public.customer_invoice_categories USING btree (customer_invoice_category_id);
--
  -- Name: customer_invoice_categories_customer_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_categories_customer_invoice_id_idx ON public.customer_invoice_categories USING btree (customer_invoice_id);
--
  -- Name: customer_invoice_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_currency_id_idx ON public.customer_invoice USING btree (currency_id);
--
  -- Name: customer_invoice_customer_bank_account_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_customer_bank_account_id_idx ON public.customer_invoice USING btree (customer_bank_account_id);
--
  -- Name: customer_invoice_customer_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_customer_country_id_idx ON public.customer_invoice USING btree (customer_country_id);
--
  -- Name: customer_invoice_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_customer_id_idx ON public.customer_invoice USING btree (customer_id);
--
  -- Name: customer_invoice_customer_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_customer_province_id_idx ON public.customer_invoice USING btree (customer_province_id);
--
  -- Name: customer_invoice_item_customer_charge_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_item_customer_charge_id_idx ON public.customer_invoice_item USING btree (customer_charge_id);
--
  -- Name: customer_invoice_item_customer_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_item_customer_invoice_id_idx ON public.customer_invoice_item USING btree (customer_invoice_id);
--
  -- Name: customer_invoice_item_vat_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_item_vat_id_idx ON public.customer_invoice_item USING btree (vat_id);
--
  -- Name: customer_invoice_numbering_schema_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_numbering_schema_id_idx ON public.customer_invoice USING btree (numbering_schema_id);
--
  -- Name: customer_invoice_payment_conditions_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_payment_conditions_id_idx ON public.customer_invoice USING btree (payment_conditions_id);
--
  -- Name: customer_invoice_payment_method_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_payment_method_id_idx ON public.customer_invoice USING btree (payment_method_id);
--
  -- Name: customer_invoice_signed_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_signed_person_id_idx ON public.customer_invoice USING btree (signed_person_id);
--
  -- Name: customer_invoice_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_template_id_idx ON public.customer_invoice USING btree (template_id);
--
  -- Name: customer_invoice_xtrf_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_invoice_xtrf_currency_id_idx ON public.customer_invoice USING btree (currency_id);
--
  -- Name: customer_language_combination_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_language_combination_customer_id_idx ON public.customer_language_combination USING btree (customer_id);
--
  -- Name: customer_language_combination_source_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_language_combination_source_language_id_idx ON public.customer_language_combination USING btree (source_language_id);
--
  -- Name: customer_language_combination_specializations_customer_language; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_language_combination_specializations_customer_language ON public.customer_language_combination_specializations USING btree (customer_language_combination_id);
--
  -- Name: customer_language_combination_specializations_language_speciali; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_language_combination_specializations_language_speciali ON public.customer_language_combination_specializations USING btree (language_specialization_id);
--
  -- Name: customer_language_combination_target_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_language_combination_target_language_id_idx ON public.customer_language_combination USING btree (target_language_id);
--
  -- Name: customer_language_specializations_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_language_specializations_customer_id_idx ON public.customer_language_specializations USING btree (customer_id);
--
  -- Name: customer_language_specializations_language_specialization_id_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_language_specializations_language_specialization_id_id ON public.customer_language_specializations USING btree (language_specialization_id);
--
  -- Name: customer_languages_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_languages_customer_id_idx ON public.customer_languages USING btree (customer_id);
--
  -- Name: customer_languages_xtrf_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_languages_xtrf_language_id_idx ON public.customer_languages USING btree (xtrf_language_id);
--
  -- Name: customer_lead_source_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_lead_source_id_idx ON public.customer USING btree (lead_source_id);
--
  -- Name: customer_limit_access_to_people_responsible_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_limit_access_to_people_responsible_idx ON public.customer USING btree (limit_access_to_people_responsible);
--
  -- Name: customer_linked_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_linked_provider_id_idx ON public.customer USING btree (linked_provider_id);
--
  -- Name: customer_minimal_charge_customer_language_combination_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_minimal_charge_customer_language_combination_id_idx ON public.customer_minimal_charge USING btree (customer_language_combination_id);
--
  -- Name: customer_minimal_charge_customer_price_profile_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_minimal_charge_customer_price_profile_id_idx ON public.customer_minimal_charge USING btree (customer_price_profile_id);
--
  -- Name: customer_name_index; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_name_index ON public.customer USING btree (name);
--
  -- Name: customer_parent_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_parent_customer_id_idx ON public.customer USING btree (parent_customer_id);
--
  -- Name: customer_payment_accepted_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_payment_accepted_currency_id_idx ON public.customer_payment USING btree (accepted_currency_id);
--
  -- Name: customer_payment_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_payment_customer_id_idx ON public.customer_payment USING btree (customer_id);
--
  -- Name: customer_payment_item_customer_charge_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_payment_item_customer_charge_id_idx ON public.customer_payment_item USING btree (customer_charge_id);
--
  -- Name: customer_payment_item_customer_payment_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_payment_item_customer_payment_id_idx ON public.customer_payment_item USING btree (customer_payment_id);
--
  -- Name: customer_payment_payment_method_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_payment_payment_method_id_idx ON public.customer_payment USING btree (payment_method_id);
--
  -- Name: customer_payment_prepayment_clearing_correlated_customer_paymen; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_payment_prepayment_clearing_correlated_customer_paymen ON public.customer_payment USING btree (
    prepayment_clearing_correlated_customer_payment_id
  );
--
  -- Name: customer_payment_received_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_payment_received_currency_id_idx ON public.customer_payment USING btree (received_currency_id);
--
  -- Name: customer_person_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_person_contact_person_id_idx ON public.customer_person USING btree (contact_person_id);
--
  -- Name: customer_person_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_person_customer_id_idx ON public.customer_person USING btree (customer_id);
--
  -- Name: customer_person_customer_person_salesforce_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_person_customer_person_salesforce_id_idx ON public.customer_person USING btree (customer_person_salesforce_id);
--
  -- Name: customer_person_preferences_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_person_preferences_id_idx ON public.customer_person USING btree (preferences_id);
--
  -- Name: customer_person_xtrf_user_group_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_person_xtrf_user_group_id_idx ON public.customer_person USING btree (xtrf_user_group_id);
--
  -- Name: customer_preferences_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_preferences_id_idx ON public.customer USING btree (preferences_id);
--
  -- Name: customer_preferred_social_media_contact_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_preferred_social_media_contact_id_idx ON public.customer USING btree (preferred_social_media_contact_id);
--
  -- Name: customer_price_list_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_price_list_currency_id_idx ON public.customer_price_list USING btree (currency_id);
--
  -- Name: customer_price_profile_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_price_profile_customer_id_idx ON public.customer_price_profile USING btree (customer_id);
--
  -- Name: customer_price_profile_default_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_price_profile_default_contact_person_id_idx ON public.customer_price_profile USING btree (default_contact_person_id);
--
  -- Name: customer_price_profile_default_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_price_profile_default_currency_id_idx ON public.customer_price_profile USING btree (default_currency_id);
--
  -- Name: customer_price_profile_price_list_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_price_profile_price_list_id_idx ON public.customer_price_profile USING btree (price_list_id);
--
  -- Name: customer_project_confirmation_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_project_confirmation_template_id_idx ON public.customer USING btree (project_confirmation_template_id);
--
  -- Name: customer_quote_confirmation_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_quote_confirmation_template_id_idx ON public.customer USING btree (quote_confirmation_template_id);
--
  -- Name: customer_quote_task_confirmation_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_quote_task_confirmation_template_id_idx ON public.customer USING btree (quote_task_confirmation_template_id);
--
  -- Name: customer_services_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_services_customer_id_idx ON public.customer_services USING btree (customer_id);
--
  -- Name: customer_services_process_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_services_process_template_id_idx ON public.customer_services USING btree (process_template_id);
--
  -- Name: customer_services_service_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_services_service_id_idx ON public.customer_services USING btree (service_id);
--
  -- Name: customer_services_workflow_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_services_workflow_id_idx ON public.customer_services USING btree (workflow_id);
--
  -- Name: customer_social_media_collection_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_social_media_collection_id_idx ON public.customer USING btree (social_media_collection_id);
--
  -- Name: customer_standard_property_container_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_standard_property_container_id_idx ON public.customer USING btree (standard_property_container_id);
--
  -- Name: customer_system_account_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_system_account_id_idx ON public.customer USING btree (system_account_id);
--
  -- Name: customer_task_confirmation_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_task_confirmation_template_id_idx ON public.customer USING btree (task_confirmation_template_id);
--
  -- Name: customer_task_files_available_email_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_task_files_available_email_template_id_idx ON public.customer USING btree (task_files_available_email_template_id);
--
  -- Name: customer_vat_rate_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_vat_rate_id_idx ON public.customer USING btree (vat_rate_id);
--
  -- Name: customer_xtrf_user_group_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX customer_xtrf_user_group_id_idx ON public.customer USING btree (xtrf_user_group_id);
--
  -- Name: external_system_project_activity_with_all_files_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX external_system_project_activity_with_all_files_id_idx ON public.external_system_project USING btree (activity_with_all_files_id);
--
  -- Name: external_system_project_external_system_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX external_system_project_external_system_id_idx ON public.external_system_project USING btree (external_system_id);
--
  -- Name: feedback_created_by_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_created_by_user_id_idx ON public.feedback USING btree (created_by_user_id);
--
  -- Name: feedback_efficiency_approved_by_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_efficiency_approved_by_user_id_idx ON public.feedback USING btree (efficiency_approved_by_user_id);
--
  -- Name: feedback_related_providers_feedback_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_related_providers_feedback_id_idx ON public.feedback_related_providers USING btree (feedback_id);
--
  -- Name: feedback_related_providers_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_related_providers_provider_id_idx ON public.feedback_related_providers USING btree (provider_id);
--
  -- Name: feedback_related_tasks_feedback_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_related_tasks_feedback_id_idx ON public.feedback_related_tasks USING btree (feedback_id);
--
  -- Name: feedback_related_tasks_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_related_tasks_task_id_idx ON public.feedback_related_tasks USING btree (task_id);
--
  -- Name: feedback_related_users_feedback_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_related_users_feedback_id_idx ON public.feedback_related_users USING btree (feedback_id);
--
  -- Name: feedback_related_users_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_related_users_user_id_idx ON public.feedback_related_users USING btree (user_id);
--
  -- Name: feedback_responsible_for_implementation_feedback_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_responsible_for_implementation_feedback_id_idx ON public.feedback_responsible_for_implementation USING btree (feedback_id);
--
  -- Name: feedback_responsible_for_implementation_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_responsible_for_implementation_user_id_idx ON public.feedback_responsible_for_implementation USING btree (user_id);
--
  -- Name: feedback_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX feedback_template_id_idx ON public.feedback USING btree (template_id);
--
  -- Name: file_stats_file_stat_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX file_stats_file_stat_id_idx ON public.file_stats USING btree (file_stat_id);
--
  -- Name: file_stats_status; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX file_stats_status ON public.workflow_job_file USING btree (file_stats_status);
--
  -- Name: idx_customer_charge_customer_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_customer_charge_customer_id ON public.customer_charge USING btree (customer_id);
--
  -- Name: idx_customer_invoice_customer_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_customer_invoice_customer_id ON public.customer_invoice USING btree (customer_id);
--
  -- Name: idx_customer_payment_customer_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_customer_payment_customer_id ON public.customer_payment USING btree (customer_id);
--
  -- Name: idx_provider_charge_provider_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_provider_charge_provider_id ON public.provider_charge USING btree (provider_id);
--
  -- Name: idx_provider_invoice_provider_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_provider_invoice_provider_id ON public.provider_invoice USING btree (provider_id);
--
  -- Name: idx_provider_payment_provider_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_provider_payment_provider_id ON public.provider_payment USING btree (provider_id);
--
  -- Name: idx_system_account_name; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_system_account_name ON public.system_account USING btree (uid);
--
  -- Name: idx_xtrf_user_login; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX idx_xtrf_user_login ON public.xtrf_user USING btree (xtrf_login);
--
  -- Name: opportunity_customer_contact_person_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_customer_contact_person_idx ON public.opportunity USING btree (customer_contact_person);
--
  -- Name: opportunity_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_customer_id_idx ON public.opportunity USING btree (customer_id);
--
  -- Name: opportunity_first_choosed_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_first_choosed_currency_id_idx ON public.opportunity USING btree (first_choosed_currency_id);
--
  -- Name: opportunity_most_probable_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_most_probable_currency_id_idx ON public.opportunity USING btree (most_probable_currency_id);
--
  -- Name: opportunity_most_probable_net_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_most_probable_net_currency_id_idx ON public.opportunity USING btree (most_probable_net_currency_id);
--
  -- Name: opportunity_offer_close_reason_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_offer_close_reason_type_id_idx ON public.opportunity_offer USING btree (close_reason_type_id);
--
  -- Name: opportunity_offer_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_offer_currency_id_idx ON public.opportunity_offer USING btree (currency_id);
--
  -- Name: opportunity_offer_opportunity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_offer_opportunity_id_idx ON public.opportunity_offer USING btree (opportunity_id);
--
  -- Name: opportunity_offer_opportunity_status_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_offer_opportunity_status_id_idx ON public.opportunity_offer USING btree (opportunity_status_id);
--
  -- Name: opportunity_offer_quote_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_offer_quote_id_idx ON public.opportunity_offer USING btree (quote_id);
--
  -- Name: opportunity_optimistic_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_optimistic_currency_id_idx ON public.opportunity USING btree (optimistic_currency_id);
--
  -- Name: opportunity_optimistic_net_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_optimistic_net_currency_id_idx ON public.opportunity USING btree (optimistic_net_currency_id);
--
  -- Name: opportunity_optimistic_status_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_optimistic_status_id_idx ON public.opportunity USING btree (optimistic_status_id);
--
  -- Name: opportunity_pessimistic_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_pessimistic_currency_id_idx ON public.opportunity USING btree (pessimistic_currency_id);
--
  -- Name: opportunity_pessimistic_net_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_pessimistic_net_currency_id_idx ON public.opportunity USING btree (pessimistic_net_currency_id);
--
  -- Name: opportunity_pessimistic_status_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_pessimistic_status_id_idx ON public.opportunity USING btree (pessimistic_status_id);
--
  -- Name: opportunity_sales_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX opportunity_sales_person_id_idx ON public.opportunity USING btree (sales_person_id);
--
  -- Name: payment_conditions_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX payment_conditions_customer_id_idx ON public.payment_conditions USING btree (customer_id);
--
  -- Name: payment_conditions_gc_system_configuration_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX payment_conditions_gc_system_configuration_id_idx ON public.payment_conditions USING btree (gc_system_configuration_id);
--
  -- Name: payment_conditions_gp_system_configuration_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX payment_conditions_gp_system_configuration_id_idx ON public.payment_conditions USING btree (gp_system_configuration_id);
--
  -- Name: payment_conditions_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX payment_conditions_provider_id_idx ON public.payment_conditions USING btree (provider_id);
--
  -- Name: person_native_languages_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX person_native_languages_contact_person_id_idx ON public.person_native_languages USING btree (contact_person_id);
--
  -- Name: person_native_languages_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX person_native_languages_language_id_idx ON public.person_native_languages USING btree (language_id);
--
  -- Name: person_native_languages_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX person_native_languages_person_id_idx ON public.person_native_languages USING btree (contact_person_id);
--
  -- Name: previous_activities_activity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX previous_activities_activity_id_idx ON public.previous_activities USING btree (activity_id);
--
  -- Name: previous_activities_previous_activity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX previous_activities_previous_activity_id_idx ON public.previous_activities USING btree (previous_activity_id);
--
  -- Name: project_account_manager_deadline_reminder_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_account_manager_deadline_reminder_id_idx ON public.project USING btree (account_manager_deadline_reminder_id);
--
  -- Name: project_account_manager_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_account_manager_id_idx ON public.project USING btree (account_manager_id);
--
  -- Name: project_additional_contact_persons_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_additional_contact_persons_person_id_idx ON public.project_additional_contact_persons USING btree (person_id);
--
  -- Name: project_additional_contact_persons_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_additional_contact_persons_project_id_idx ON public.project_additional_contact_persons USING btree (project_id);
--
  -- Name: project_archived_directories_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_archived_directories_project_id_idx ON public.project_archived_directories USING btree (project_id);
--
  -- Name: project_assisted_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_assisted_project_id_idx ON public.project USING btree (assisted_project_id);
--
  -- Name: project_categories_project_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_categories_project_category_id_idx ON public.project_categories USING btree (project_category_id);
--
  -- Name: project_categories_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_categories_project_id_idx ON public.project_categories USING btree (project_id);
--
  -- Name: project_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_currency_id_idx ON public.project USING btree (currency_id);
--
  -- Name: project_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_custom_fields_id_idx ON public.project USING btree (custom_fields_id);
--
  -- Name: project_customer_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_customer_contact_person_id_idx ON public.project USING btree (customer_contact_person_id);
--
  -- Name: project_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_customer_id_idx ON public.project USING btree (customer_id);
--
  -- Name: project_customer_price_profile_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_customer_price_profile_id_idx ON public.project USING btree (customer_price_profile_id);
--
  -- Name: project_language_combination_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_language_combination_project_id_idx ON public.project_language_combination USING btree (project_id);
--
  -- Name: project_language_combination_source_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_language_combination_source_language_id_idx ON public.project_language_combination USING btree (source_language_id);
--
  -- Name: project_language_combination_target_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_language_combination_target_language_id_idx ON public.project_language_combination USING btree (target_language_id);
--
  -- Name: project_language_specialization_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_language_specialization_id_idx ON public.project USING btree (language_specialization_id);
--
  -- Name: project_project_coordinator_deadline_reminder_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_project_coordinator_deadline_reminder_id_idx ON public.project USING btree (project_coordinator_deadline_reminder_id);
--
  -- Name: project_project_coordinator_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_project_coordinator_id_idx ON public.project USING btree (project_coordinator_id);
--
  -- Name: project_project_manager_deadline_reminder_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_project_manager_deadline_reminder_id_idx ON public.project USING btree (project_manager_deadline_reminder_id);
--
  -- Name: project_project_manager_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_project_manager_id_idx ON public.project USING btree (project_manager_id);
--
  -- Name: project_quote_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_quote_id_idx ON public.project USING btree (quote_id);
--
  -- Name: project_sales_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_sales_person_id_idx ON public.project USING btree (sales_person_id);
--
  -- Name: project_send_back_to_customer_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_send_back_to_customer_contact_person_id_idx ON public.project USING btree (send_back_to_customer_contact_person_id);
--
  -- Name: project_service_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_service_id_idx ON public.project USING btree (service_id);
--
  -- Name: project_standard_property_container_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_standard_property_container_id_idx ON public.project USING btree (standard_property_container_id);
--
  -- Name: project_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_template_id_idx ON public.project USING btree (template_id);
--
  -- Name: project_workflow_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX project_workflow_id_idx ON public.project USING btree (workflow_id);
--
  -- Name: provider_accountency_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_accountency_contact_person_id_idx ON public.provider USING btree (accountency_contact_person_id);
--
  -- Name: provider_address_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_address_country_id_idx ON public.provider USING btree (address_country_id);
--
  -- Name: provider_address_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_address_province_id_idx ON public.provider USING btree (address_province_id);
--
  -- Name: provider_automated_activity_action_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_automated_activity_action_id_idx ON public.provider USING btree (automated_activity_action_id);
--
  -- Name: provider_billing_data_correspondence_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_billing_data_correspondence_country_id_idx ON public.provider_billing_data USING btree (correspondence_country_id);
--
  -- Name: provider_billing_data_correspondence_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_billing_data_correspondence_province_id_idx ON public.provider_billing_data USING btree (correspondence_province_id);
--
  -- Name: provider_billing_data_treasury_office_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_billing_data_treasury_office_id_idx ON public.provider_billing_data USING btree (treasury_office_id);
--
  -- Name: provider_branch_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_branch_id_idx ON public.provider USING btree (branch_id);
--
  -- Name: provider_categories_project_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_categories_project_category_id_idx ON public.provider_categories USING btree (project_category_id);
--
  -- Name: provider_categories_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_categories_provider_id_idx ON public.provider_categories USING btree (provider_id);
--
  -- Name: provider_charge_charge_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_charge_charge_type_id_idx ON public.provider_charge USING btree (charge_type_id);
--
  -- Name: provider_charge_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_charge_currency_id_idx ON public.provider_charge USING btree (currency_id);
--
  -- Name: provider_charge_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_charge_provider_id_idx ON public.provider_charge USING btree (provider_id);
--
  -- Name: provider_charge_provider_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_charge_provider_invoice_id_idx ON public.provider_charge USING btree (provider_invoice_id);
--
  -- Name: provider_correspondence_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_correspondence_country_id_idx ON public.provider USING btree (correspondence_country_id);
--
  -- Name: provider_correspondence_province_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_correspondence_province_id_idx ON public.provider USING btree (correspondence_province_id);
--
  -- Name: provider_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_custom_fields_id_idx ON public.provider USING btree (custom_fields_id);
--
  -- Name: provider_default_payment_conditions_id_for_empty_invoice_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_default_payment_conditions_id_for_empty_invoice_idx ON public.provider USING btree (default_payment_conditions_id_for_empty_invoice);
--
  -- Name: provider_default_payment_conditions_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_default_payment_conditions_id_idx ON public.provider USING btree (default_payment_conditions_id);
--
  -- Name: provider_evaluation_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_evaluation_template_id_idx ON public.provider USING btree (evaluation_template_id);
--
  -- Name: provider_invoice_accountency_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_accountency_person_id_idx ON public.provider_invoice USING btree (accountency_person_id);
--
  -- Name: provider_invoice_categories_provider_invoice_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_categories_provider_invoice_category_id_idx ON public.provider_invoice_categories USING btree (provider_invoice_category_id);
--
  -- Name: provider_invoice_categories_provider_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_categories_provider_invoice_id_idx ON public.provider_invoice_categories USING btree (provider_invoice_id);
--
  -- Name: provider_invoice_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_currency_id_idx ON public.provider_invoice USING btree (currency_id);
--
  -- Name: provider_invoice_payment_conditions_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_payment_conditions_id_idx ON public.provider_invoice USING btree (payment_conditions_id);
--
  -- Name: provider_invoice_payment_method_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_payment_method_id_idx ON public.provider_invoice USING btree (payment_method_id);
--
  -- Name: provider_invoice_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_provider_id_idx ON public.provider_invoice USING btree (provider_id);
--
  -- Name: provider_invoice_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_template_id_idx ON public.provider USING btree (invoice_template_id);
--
  -- Name: provider_invoice_xtrf_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_invoice_xtrf_currency_id_idx ON public.provider_invoice USING btree (currency_id);
--
  -- Name: provider_language_combination_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_language_combination_provider_id_idx ON public.provider_language_combination USING btree (provider_id);
--
  -- Name: provider_language_combination_source_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_language_combination_source_language_id_idx ON public.provider_language_combination USING btree (source_language_id);
--
  -- Name: provider_language_combination_target_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_language_combination_target_language_id_idx ON public.provider_language_combination USING btree (target_language_id);
--
  -- Name: provider_lead_source_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_lead_source_id_idx ON public.provider USING btree (lead_source_id);
--
  -- Name: provider_multiple_purchase_order_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_multiple_purchase_order_template_id_idx ON public.provider USING btree (multiple_purchase_order_template_id);
--
  -- Name: provider_name_index; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_name_index ON public.provider USING btree (name);
--
  -- Name: provider_payment_accepted_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_payment_accepted_currency_id_idx ON public.provider_payment USING btree (accepted_currency_id);
--
  -- Name: provider_payment_item_provider_charge_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_payment_item_provider_charge_id_idx ON public.provider_payment_item USING btree (provider_charge_id);
--
  -- Name: provider_payment_item_provider_payment_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_payment_item_provider_payment_id_idx ON public.provider_payment_item USING btree (provider_payment_id);
--
  -- Name: provider_payment_payment_method_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_payment_payment_method_id_idx ON public.provider_payment USING btree (payment_method_id);
--
  -- Name: provider_payment_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_payment_provider_id_idx ON public.provider_payment USING btree (provider_id);
--
  -- Name: provider_payment_received_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_payment_received_currency_id_idx ON public.provider_payment USING btree (received_currency_id);
--
  -- Name: provider_person_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_person_contact_person_id_idx ON public.provider_person USING btree (contact_person_id);
--
  -- Name: provider_person_preferences_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_person_preferences_id_idx ON public.provider_person USING btree (preferences_id);
--
  -- Name: provider_person_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_person_provider_id_idx ON public.provider_person USING btree (provider_id);
--
  -- Name: provider_preferences_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_preferences_id_idx ON public.provider USING btree (preferences_id);
--
  -- Name: provider_preferred_social_media_contact_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_preferred_social_media_contact_id_idx ON public.provider USING btree (preferred_social_media_contact_id);
--
  -- Name: provider_previous_activity_partially_finished_email_template_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_previous_activity_partially_finished_email_template_id ON public.provider USING btree (
    previous_activity_partially_finished_email_template_id
  );
--
  -- Name: provider_previous_activity_ready_email_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_previous_activity_ready_email_template_id_idx ON public.provider USING btree (previous_activity_ready_email_template_id);
--
  -- Name: provider_provider_experience_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_provider_experience_id_idx ON public.provider USING btree (provider_experience_id);
--
  -- Name: provider_provider_rating_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_provider_rating_id_idx ON public.provider USING btree (provider_rating_id);
--
  -- Name: provider_purchase_order_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_purchase_order_template_id_idx ON public.provider USING btree (purchase_order_template_id);
--
  -- Name: provider_social_media_collection_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_social_media_collection_id_idx ON public.provider USING btree (social_media_collection_id);
--
  -- Name: provider_standard_property_container_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_standard_property_container_id_idx ON public.provider USING btree (standard_property_container_id);
--
  -- Name: provider_system_account_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_system_account_id_idx ON public.provider USING btree (system_account_id);
--
  -- Name: provider_vat_rate_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX provider_vat_rate_id_idx ON public.provider USING btree (vat_rate_id);
--
  -- Name: province_country_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX province_country_id_idx ON public.province USING btree (country_id);
--
  -- Name: quote_account_manager_expiry_reminder_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_account_manager_expiry_reminder_id_idx ON public.quote USING btree (account_manager_expiry_reminder_id);
--
  -- Name: quote_account_manager_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_account_manager_id_idx ON public.quote USING btree (account_manager_id);
--
  -- Name: quote_additional_contact_persons_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_additional_contact_persons_person_id_idx ON public.quote_additional_contact_persons USING btree (person_id);
--
  -- Name: quote_additional_contact_persons_quote_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_additional_contact_persons_quote_id_idx ON public.quote_additional_contact_persons USING btree (quote_id);
--
  -- Name: quote_assisted_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_assisted_project_id_idx ON public.quote USING btree (assisted_project_id);
--
  -- Name: quote_categories_project_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_categories_project_category_id_idx ON public.quote_categories USING btree (project_category_id);
--
  -- Name: quote_categories_quote_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_categories_quote_id_idx ON public.quote_categories USING btree (quote_id);
--
  -- Name: quote_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_currency_id_idx ON public.quote USING btree (currency_id);
--
  -- Name: quote_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_custom_fields_id_idx ON public.quote USING btree (custom_fields_id);
--
  -- Name: quote_customer_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_customer_contact_person_id_idx ON public.quote USING btree (customer_contact_person_id);
--
  -- Name: quote_customer_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_customer_id_idx ON public.quote USING btree (customer_id);
--
  -- Name: quote_customer_price_profile_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_customer_price_profile_id_idx ON public.quote USING btree (customer_price_profile_id);
--
  -- Name: quote_language_combination_quote_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_language_combination_quote_id_idx ON public.quote_language_combination USING btree (quote_id);
--
  -- Name: quote_language_combination_source_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_language_combination_source_language_id_idx ON public.quote_language_combination USING btree (source_language_id);
--
  -- Name: quote_language_combination_target_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_language_combination_target_language_id_idx ON public.quote_language_combination USING btree (target_language_id);
--
  -- Name: quote_language_specialization_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_language_specialization_id_idx ON public.quote USING btree (language_specialization_id);
--
  -- Name: quote_project_coordinator_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_project_coordinator_id_idx ON public.quote USING btree (project_coordinator_id);
--
  -- Name: quote_project_manager_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_project_manager_id_idx ON public.quote USING btree (project_manager_id);
--
  -- Name: quote_rejection_reason_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_rejection_reason_id_idx ON public.quote USING btree (rejection_reason_id);
--
  -- Name: quote_sales_person_expiry_reminder_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_sales_person_expiry_reminder_id_idx ON public.quote USING btree (sales_person_expiry_reminder_id);
--
  -- Name: quote_sales_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_sales_person_id_idx ON public.quote USING btree (sales_person_id);
--
  -- Name: quote_send_back_to_customer_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_send_back_to_customer_contact_person_id_idx ON public.quote USING btree (send_back_to_customer_contact_person_id);
--
  -- Name: quote_service_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_service_id_idx ON public.quote USING btree (service_id);
--
  -- Name: quote_standard_property_container_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_standard_property_container_id_idx ON public.quote USING btree (standard_property_container_id);
--
  -- Name: quote_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_template_id_idx ON public.quote USING btree (template_id);
--
  -- Name: quote_workflow_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX quote_workflow_id_idx ON public.quote USING btree (workflow_id);
-- Name: service_activity_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX service_activity_type_id_idx ON public.service USING btree (activity_type_id);
--
  -- Name: service_process_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX service_process_template_id_idx ON public.service USING btree (process_template_id);
--
  -- Name: service_workflow_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX service_workflow_id_idx ON public.service USING btree (workflow_id);
--
  -- Name: task_amount_modifiers_amount_modifier_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_amount_modifiers_amount_modifier_id_idx ON public.task_amount_modifiers USING btree (amount_modifier_id);
--
  -- Name: task_amount_modifiers_task_finance_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_amount_modifiers_task_finance_id_idx ON public.task_amount_modifiers USING btree (task_finance_id);
--
  -- Name: task_bundles_meta_directory_when_embeeded_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_bundles_meta_directory_when_embeeded_id_idx ON public.task USING btree (bundles_meta_directory_when_embeeded_id);
--
  -- Name: task_cat_charge_activity_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_activity_type_id_idx ON public.task_cat_charge USING btree (activity_type_id);
--
  -- Name: task_cat_charge_amount_modifiers_amount_modifier_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_amount_modifiers_amount_modifier_id_idx ON public.task_cat_charge_amount_modifiers USING btree (amount_modifier_id);
--
  -- Name: task_cat_charge_amount_modifiers_task_cat_charge_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_amount_modifiers_task_cat_charge_id_idx ON public.task_cat_charge_amount_modifiers USING btree (task_cat_charge_id);
--
  -- Name: task_cat_charge_assisted_automated_receivable_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_assisted_automated_receivable_id_idx ON public.task_cat_charge USING btree (assisted_automated_receivable_id);
--
  -- Name: task_cat_charge_calculation_unit_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_calculation_unit_id_idx ON public.task_cat_charge USING btree (calculation_unit_id);
--
  -- Name: task_cat_charge_task_finance_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_task_finance_id ON public.task_cat_charge USING btree (task_finance_id);
--
  -- Name: task_cat_charge_task_finance_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_task_finance_id_idx ON public.task_cat_charge USING btree (task_finance_id);
--
  -- Name: task_cat_charge_tm_savings_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_cat_charge_tm_savings_id_idx ON public.task_cat_charge USING btree (tm_savings_id);
--
  -- Name: task_categories_project_category_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_categories_project_category_id_idx ON public.task_categories USING btree (project_category_id);
--
  -- Name: task_categories_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_categories_task_id_idx ON public.task_categories USING btree (task_id);
--
  -- Name: task_charge_activity_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_charge_activity_type_id_idx ON public.task_charge USING btree (activity_type_id);
--
  -- Name: task_charge_amount_modifiers_amount_modifier_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_charge_amount_modifiers_amount_modifier_id_idx ON public.task_charge_amount_modifiers USING btree (amount_modifier_id);
--
  -- Name: task_charge_amount_modifiers_task_charge_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_charge_amount_modifiers_task_charge_id_idx ON public.task_charge_amount_modifiers USING btree (task_charge_id);
--
  -- Name: task_charge_assisted_automated_receivable_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_charge_assisted_automated_receivable_id_idx ON public.task_charge USING btree (assisted_automated_receivable_id);
--
  -- Name: task_charge_calculation_unit_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_charge_calculation_unit_id_idx ON public.task_charge USING btree (calculation_unit_id);
--
  -- Name: task_charge_task_finance_id; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_charge_task_finance_id ON public.task_charge USING btree (task_finance_id);
--
  -- Name: task_charge_task_finance_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_charge_task_finance_id_idx ON public.task_charge USING btree (task_finance_id);
--
  -- Name: task_claim_created_by_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_created_by_user_id_idx ON public.feedback USING btree (created_by_user_id);
--
  -- Name: task_claim_efficiency_approved_by_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_efficiency_approved_by_user_id_idx ON public.feedback USING btree (efficiency_approved_by_user_id);
--
  -- Name: task_claim_related_providers_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_related_providers_provider_id_idx ON public.feedback_related_providers USING btree (provider_id);
--
  -- Name: task_claim_related_providers_task_claim_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_related_providers_task_claim_id_idx ON public.feedback_related_providers USING btree (feedback_id);
--
  -- Name: task_claim_related_users_task_claim_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_related_users_task_claim_id_idx ON public.feedback_related_users USING btree (feedback_id);
--
  -- Name: task_claim_related_users_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_related_users_user_id_idx ON public.feedback_related_users USING btree (user_id);
--
  -- Name: task_claim_responsible_for_implementation_task_claim_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_responsible_for_implementation_task_claim_id_idx ON public.feedback_responsible_for_implementation USING btree (feedback_id);
--
  -- Name: task_claim_responsible_for_implementation_user_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_claim_responsible_for_implementation_user_id_idx ON public.feedback_responsible_for_implementation USING btree (user_id);
--
  -- Name: task_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_custom_fields_id_idx ON public.task USING btree (custom_fields_id);
--
  -- Name: task_customer_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_customer_contact_person_id_idx ON public.task USING btree (customer_contact_person_id);
--
  -- Name: task_customer_invoice_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_customer_invoice_id_idx ON public.task USING btree (customer_invoice_id);
--
  -- Name: task_external_system_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_external_system_project_id_idx ON public.task USING btree (external_system_project_id);
--
  -- Name: task_finance_currency_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_finance_currency_id_idx ON public.task_finance USING btree (currency_id);
--
  -- Name: task_finance_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_finance_task_id_idx ON public.task_finance USING btree (task_id);
--
  -- Name: task_language_specialization_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_language_specialization_id_idx ON public.task USING btree (language_specialization_id);
--
  -- Name: task_payment_conditions_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_payment_conditions_id_idx ON public.task USING btree (payment_conditions_id);
--
  -- Name: task_project_coordinator_deadline_reminder_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_project_coordinator_deadline_reminder_id_idx ON public.task USING btree (project_coordinator_deadline_reminder_id);
--
  -- Name: task_project_coordinator_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_project_coordinator_id_idx ON public.task USING btree (project_coordinator_id);
--
  -- Name: task_project_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_project_id_idx ON public.task USING btree (project_id);
--
  -- Name: task_project_manager_deadline_reminder_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_project_manager_deadline_reminder_id_idx ON public.task USING btree (project_manager_deadline_reminder_id);
--
  -- Name: task_project_manager_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_project_manager_id_idx ON public.task USING btree (project_manager_id);
--
  -- Name: task_project_part_finance_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_project_part_finance_id_idx ON public.task USING btree (project_part_finance_id);
--
  -- Name: task_project_part_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_project_part_template_id_idx ON public.task USING btree (project_part_template_id);
--
  -- Name: task_provider_selection_settings_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_provider_selection_settings_id_idx ON public.task USING btree (provider_selection_settings_id);
--
  -- Name: task_quote_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_quote_id_idx ON public.task USING btree (quote_id);
--
  -- Name: task_quote_part_finance_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_quote_part_finance_id_idx ON public.task USING btree (quote_part_finance_id);
--
  -- Name: task_quote_part_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_quote_part_template_id_idx ON public.task USING btree (quote_part_template_id);
--
  -- Name: task_send_back_to_customer_contact_person_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_send_back_to_customer_contact_person_id_idx ON public.task USING btree (send_back_to_customer_contact_person_id);
--
  -- Name: task_source_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_source_language_id_idx ON public.task USING btree (source_language_id);
--
  -- Name: task_standard_property_container_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_standard_property_container_id_idx ON public.task USING btree (standard_property_container_id);
--
  -- Name: task_status; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_status ON public.task USING btree (status);
--
  -- Name: task_status_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_status_task_id_idx ON public.task USING btree (status, task_id);
--
  -- Name: task_target_language_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_target_language_id_idx ON public.task USING btree (target_language_id);
--
  -- Name: task_task_workflow_job_instance_when_embeeded_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_task_workflow_job_instance_when_embeeded_id_idx ON public.task USING btree (task_workflow_job_instance_when_embeeded_id);
--
  -- Name: task_vat_rate_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_vat_rate_id_idx ON public.task USING btree (vat_rate_id);
--
  -- Name: task_workflow_definition_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_workflow_definition_id_idx ON public.task USING btree (workflow_definition_id);
--
  -- Name: task_workflow_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_workflow_id_idx ON public.task USING btree (workflow_id);
--
  -- Name: task_workflow_job_instance_task_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_workflow_job_instance_task_task_id_idx ON public.task_workflow_job_instance USING btree (task_task_id);
--
  -- Name: task_workflow_job_instance_workflowjobinstances_workflow_job_in; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX task_workflow_job_instance_workflowjobinstances_workflow_job_in ON public.task_workflow_job_instance USING btree (workflowjobinstances_workflow_job_instance_id);
--
  -- Name: tm_rates_customer_price_profile_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX tm_rates_customer_price_profile_id_idx ON public.tm_rates USING btree (customer_price_profile_id);
--
  -- Name: tm_rates_item_tm_rates_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX tm_rates_item_tm_rates_id_idx ON public.tm_rates_item USING btree (tm_rates_id);
--
  -- Name: tm_rates_provider_price_profile_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX tm_rates_provider_price_profile_id_idx ON public.tm_rates USING btree (provider_price_profile_id);
--
  -- Name: tm_rates_system_configuration_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX tm_rates_system_configuration_id_idx ON public.tm_rates USING btree (system_configuration_id);
--
  -- Name: tm_savings_item_tm_savings_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX tm_savings_item_tm_savings_id_idx ON public.tm_savings_item USING btree (tm_savings_id);
--
  -- Name: workflow_default_task_workflow_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_default_task_workflow_id_idx ON public.workflow USING btree (default_task_workflow_id);
--
  -- Name: workflow_external_system_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_external_system_id_idx ON public.workflow USING btree (external_system_id);
--
  -- Name: workflow_job_activity_type_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_activity_type_id_idx ON public.workflow_job USING btree (activity_type_id);
--
  -- Name: workflow_job_bundle_schema_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_bundle_schema_id_idx ON public.workflow_job USING btree (bundle_schema_id);
--
  -- Name: workflow_job_bundles_meta_directory_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_bundles_meta_directory_id_idx ON public.workflow_job USING btree (bundles_meta_directory_id);
--
  -- Name: workflow_job_file_activity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_file_activity_id_idx ON public.workflow_job_file USING btree (activity_id);
--
  -- Name: workflow_job_file_base_dir_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_file_base_dir_idx ON public.workflow_job_file USING btree (base_dir);
--
  -- Name: workflow_job_file_external_system_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_file_external_system_id_idx ON public.workflow_job_file USING btree (external_system_id);
--
  -- Name: workflow_job_file_linked_workflow_job_file_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_file_linked_workflow_job_file_id_idx ON public.workflow_job_file USING btree (linked_workflow_job_file_id);
--
  -- Name: workflow_job_file_loose_bundle_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_file_loose_bundle_id_idx ON public.workflow_job_file USING btree (loose_bundle_id);
--
  -- Name: workflow_job_file_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_file_task_id_idx ON public.workflow_job_file USING btree (task_id);
--
  -- Name: workflow_job_file_task_output_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_file_task_output_id_idx ON public.workflow_job_file USING btree (task_output_id);
--
  -- Name: workflow_job_instance_all_bundles_activity_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_instance_all_bundles_activity_id_idx ON public.workflow_job_instance USING btree (all_bundles_activity_id);
--
  -- Name: workflow_job_instance_all_bundles_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_instance_all_bundles_task_id_idx ON public.workflow_job_instance USING btree (all_bundles_task_id);
--
  -- Name: workflow_job_instance_project_task_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_instance_project_task_id_idx ON public.workflow_job_instance USING btree (project_task_id);
--
  -- Name: workflow_job_instance_workflow_job_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_instance_workflow_job_id_idx ON public.workflow_job_instance USING btree (workflow_job_id);
--
  -- Name: workflow_job_payable_calculation_unit_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_payable_calculation_unit_id_idx ON public.workflow_job USING btree (payable_calculation_unit_id);
--
  -- Name: workflow_job_phase_workflow_job_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_phase_workflow_job_id_idx ON public.workflow_job_phase USING btree (workflow_job_id);
--
  -- Name: workflow_job_provider_price_profile_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_provider_price_profile_id_idx ON public.workflow_job USING btree (provider_price_profile_id);
--
  -- Name: workflow_job_provider_selection_rules_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_provider_selection_rules_id_idx ON public.workflow_job USING btree (provider_selection_rules_id);
--
  -- Name: workflow_job_standard_property_container_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_standard_property_container_id_idx ON public.workflow_job USING btree (standard_property_container_id);
--
  -- Name: workflow_job_task_workflow_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_task_workflow_id_idx ON public.workflow_job USING btree (task_workflow_id);
--
  -- Name: workflow_job_user_defined_activity_partially_finished_email_tem; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_user_defined_activity_partially_finished_email_tem ON public.workflow_job USING btree (
    user_defined_activity_partially_finished_email_template_id
  );
--
  -- Name: workflow_job_user_defined_activity_ready_email_template_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_user_defined_activity_ready_email_template_id_idx ON public.workflow_job USING btree (user_defined_activity_ready_email_template_id);
--
  -- Name: workflow_job_workflow_definition_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_job_workflow_definition_id_idx ON public.workflow_job USING btree (workflow_definition_id);
--
  -- Name: workflow_provider_selection_settings_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_provider_selection_settings_id_idx ON public.workflow USING btree (provider_selection_settings_id);
--
  -- Name: workflow_standard_property_container_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_standard_property_container_id_idx ON public.workflow USING btree (standard_property_container_id);
--
  -- Name: workflow_workflow_definition_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_workflow_definition_id_idx ON public.workflow USING btree (workflow_definition_id);
--
  -- Name: workflow_workflow_meta_directories_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX workflow_workflow_meta_directories_id_idx ON public.workflow USING btree (workflow_meta_directories_id);
--
  -- Name: xtrf_language_symbol_key; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE UNIQUE INDEX xtrf_language_symbol_key ON public.xtrf_language USING btree (lower((symbol) :: text));
--
  -- Name: xtrf_user_branch_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_branch_id_idx ON public.xtrf_user USING btree (branch_id);
--
  -- Name: xtrf_user_custom_fields_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_custom_fields_id_idx ON public.xtrf_user USING btree (custom_fields_id);
--
  -- Name: xtrf_user_group_leader_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_group_leader_id_idx ON public.xtrf_user_group USING btree (leader_id);
--
  -- Name: xtrf_user_linked_provider_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_linked_provider_id_idx ON public.xtrf_user USING btree (linked_provider_id);
--
  -- Name: xtrf_user_person_position_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_person_position_id_idx ON public.xtrf_user USING btree (person_position_id);
--
  -- Name: xtrf_user_preferences_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_preferences_id_idx ON public.xtrf_user USING btree (preferences_id);
--
  -- Name: xtrf_user_preferred_social_media_contact_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_preferred_social_media_contact_id_idx ON public.xtrf_user USING btree (preferred_social_media_contact_id);
--
  -- Name: xtrf_user_social_media_collection_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_social_media_collection_id_idx ON public.xtrf_user USING btree (social_media_collection_id);
--
  -- Name: xtrf_user_xtrf_user_group_id_idx; Type: INDEX; Schema: public; Owner: avantdata
  --
  CREATE INDEX xtrf_user_xtrf_user_group_id_idx ON public.xtrf_user USING btree (xtrf_user_group_id);
ALTER TABLE workflow_job
ADD
  CONSTRAINT FK_BB94AED5C51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE workflow_job
ADD
  CONSTRAINT FK_BB94AED55822F70F FOREIGN KEY (payable_calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE workflow_job
ADD
  CONSTRAINT FK_BB94AED56A098188 FOREIGN KEY (task_workflow_id) REFERENCES workflow (workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE xtrf_user
ADD
  CONSTRAINT FK_36A69603DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (branch_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_related_users
ADD
  CONSTRAINT FK_709F9D69A76ED395 FOREIGN KEY (user_id) REFERENCES xtrf_user (xtrf_user_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_related_users
ADD
  CONSTRAINT FK_709F9D69D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (feedback_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_responsible_for_implementation
ADD
  CONSTRAINT FK_7E37ADB0A76ED395 FOREIGN KEY (user_id) REFERENCES xtrf_user (xtrf_user_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_responsible_for_implementation
ADD
  CONSTRAINT FK_7E37ADB0D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (feedback_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_person
ADD
  CONSTRAINT FK_63ED32E64F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_person
ADD
  CONSTRAINT FK_63ED32E6A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_payment
ADD
  CONSTRAINT FK_9D7026AA8F544A4E FOREIGN KEY (accepted_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_payment
ADD
  CONSTRAINT FK_9D7026AACE3D2663 FOREIGN KEY (received_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_payment
ADD
  CONSTRAINT FK_9D7026AAA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB255B9B6521 FOREIGN KEY (customer_contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25B8A4E203 FOREIGN KEY (external_system_project_id) REFERENCES external_system_project (external_system_project_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25685ACD4E FOREIGN KEY (language_specialization_id) REFERENCES language_specialization (language_specialization_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25B495491C FOREIGN KEY (send_back_to_customer_contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB2543897540 FOREIGN KEY (vat_rate_id) REFERENCES vat_rate (vat_rate_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB252C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow (workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25D440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25BE8EEA54 FOREIGN KEY (source_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB255CBF5FE FOREIGN KEY (target_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB2538FFD5C2 FOREIGN KEY (payment_conditions_id) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25E304DE4 FOREIGN KEY (project_coordinator_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB2560984F51 FOREIGN KEY (project_manager_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB2553C356F6 FOREIGN KEY (project_part_finance_id) REFERENCES task_finance (task_finance_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25DB805178 FOREIGN KEY (quote_id) REFERENCES quote (quote_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB258551CC76 FOREIGN KEY (quote_part_finance_id) REFERENCES task_finance (task_finance_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB25DEE76C73 FOREIGN KEY (source_ref_language_id) REFERENCES language (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task
ADD
  CONSTRAINT FK_527EDB253AF6A01B FOREIGN KEY (target_ref_language_id) REFERENCES language (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_categories
ADD
  CONSTRAINT FK_26E00DC78DB60186 FOREIGN KEY (task_id) REFERENCES task (task_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_categories
ADD
  CONSTRAINT FK_26E00DC7DA896A19 FOREIGN KEY (project_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_related_tasks
ADD
  CONSTRAINT FK_34445D178DB60186 FOREIGN KEY (task_id) REFERENCES task (task_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_related_tasks
ADD
  CONSTRAINT FK_34445D17D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (feedback_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_workflow_job_instance
ADD
  CONSTRAINT FK_5CAB5CE692572B6C FOREIGN KEY (task_task_id) REFERENCES task (task_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_workflow_job_instance
ADD
  CONSTRAINT FK_5CAB5CE6F774DD7F FOREIGN KEY (workflowjobinstances_workflow_job_instance_id) REFERENCES workflow_job_instance (workflow_job_instance_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE service
ADD
  CONSTRAINT FK_E19D9AD22C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow (workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE service
ADD
  CONSTRAINT FK_E19D9AD2C51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_payment_item
ADD
  CONSTRAINT FK_30407173FED5B10E FOREIGN KEY (customer_charge_id) REFERENCES customer_charge (customer_charge_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_payment_item
ADD
  CONSTRAINT FK_30407173F90AA739 FOREIGN KEY (customer_payment_id) REFERENCES customer_payment (customer_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote_language_combination
ADD
  CONSTRAINT FK_D142E3E7DB805178 FOREIGN KEY (quote_id) REFERENCES quote (quote_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote_language_combination
ADD
  CONSTRAINT FK_D142E3E7BE8EEA54 FOREIGN KEY (source_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote_language_combination
ADD
  CONSTRAINT FK_D142E3E75CBF5FE FOREIGN KEY (target_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice
ADD
  CONSTRAINT FK_8CB8B3FA38248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice
ADD
  CONSTRAINT FK_8CB8B3FA38FFD5C2 FOREIGN KEY (payment_conditions_id) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice
ADD
  CONSTRAINT FK_8CB8B3FA48689252 FOREIGN KEY (accountency_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice
ADD
  CONSTRAINT FK_8CB8B3FA9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice
ADD
  CONSTRAINT FK_8CB8B3FA69E3DF96 FOREIGN KEY (customer_bank_account_id) REFERENCES account (account_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice
ADD
  CONSTRAINT FK_8CB8B3FAECCD61BB FOREIGN KEY (signed_person_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice_accountency_persons
ADD
  CONSTRAINT FK_F50A93AFD440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice_accountency_persons
ADD
  CONSTRAINT FK_F50A93AF8A86435D FOREIGN KEY (customer_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice_categories
ADD
  CONSTRAINT FK_A03D4BD2D440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice_categories
ADD
  CONSTRAINT FK_A03D4BD23968097D FOREIGN KEY (customer_invoice_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice_item
ADD
  CONSTRAINT FK_13068462D440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice_item
ADD
  CONSTRAINT FK_13068462FED5B10E FOREIGN KEY (customer_charge_id) REFERENCES customer_charge (customer_charge_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_invoice_item
ADD
  CONSTRAINT FK_13068462B5B63A6B FOREIGN KEY (vat_id) REFERENCES vat_rate (vat_rate_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE province
ADD
  CONSTRAINT FK_4ADAD40BF92F3E70 FOREIGN KEY (country_id) REFERENCES country (country_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback
ADD
  CONSTRAINT FK_D22944587D182D95 FOREIGN KEY (created_by_user_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback
ADD
  CONSTRAINT FK_D2294458E902A4F3 FOREIGN KEY (efficiency_approved_by_user_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_type
ADD
  CONSTRAINT FK_568E7DGDADE68D8F FOREIGN KEY (velocity_calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_type_calculation_units
ADD
  CONSTRAINT FK_568E7DFCFDE68D7D FOREIGN KEY (calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_type_calculation_units
ADD
  CONSTRAINT FK_568E7DFCC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE account
ADD
  CONSTRAINT FK_7D3656A474B55F40 FOREIGN KEY (xtrf_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE account
ADD
  CONSTRAINT FK_7D3656A4666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE account
ADD
  CONSTRAINT FK_7D3656A49395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE account
ADD
  CONSTRAINT FK_7D3656A4A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_language_combination
ADD
  CONSTRAINT FK_95D49DD8BE8EEA54 FOREIGN KEY (source_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_language_combination
ADD
  CONSTRAINT FK_95D49DD85CBF5FE FOREIGN KEY (target_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_language_combination
ADD
  CONSTRAINT FK_95D49DD8A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE tm_rates
ADD
  CONSTRAINT FK_D8778B2568D34FA0 FOREIGN KEY (customer_price_profile_id) REFERENCES customer_price_profile (customer_price_profile_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE analytics_project_step
ADD
  CONSTRAINT FK_A4B70CF66D9546F FOREIGN KEY (analytics_project_id) REFERENCES analytics_project (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE analytics_project_step
ADD
  CONSTRAINT FK_A4B70CF6CE6064C2 FOREIGN KEY (xtrf_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_cat_charge
ADD
  CONSTRAINT FK_DE8E7F49FDE68D7D FOREIGN KEY (calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_cat_charge
ADD
  CONSTRAINT FK_DE8E7F49F393F8EE FOREIGN KEY (tm_savings_id) REFERENCES tm_savings (tm_savings_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_cat_charge
ADD
  CONSTRAINT FK_DE8E7F4981C06096 FOREIGN KEY (activity_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF484A5C6C7 FOREIGN KEY (account_manager_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF45B9B6521 FOREIGN KEY (customer_contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF438248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF49395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF468D34FA0 FOREIGN KEY (customer_price_profile_id) REFERENCES customer_price_profile (customer_price_profile_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF4685ACD4E FOREIGN KEY (language_specialization_id) REFERENCES language_specialization (language_specialization_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF4E304DE4 FOREIGN KEY (project_coordinator_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF460984F51 FOREIGN KEY (project_manager_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF4B495491C FOREIGN KEY (send_back_to_customer_contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF42C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow (workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF4666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF41D35E30E FOREIGN KEY (sales_person_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF4ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (service_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote
ADD
  CONSTRAINT FK_6B71CBF451BD4E15 FOREIGN KEY (rejection_reason_id) REFERENCES rejection_reason (rejection_reason_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote_categories
ADD
  CONSTRAINT FK_8AA43CD3DB805178 FOREIGN KEY (quote_id) REFERENCES quote (quote_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote_categories
ADD
  CONSTRAINT FK_8AA43CD3DA896A19 FOREIGN KEY (project_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE quote_additional_contact_persons
ADD
  CONSTRAINT FK_288A76418A86435D FOREIGN KEY (customer_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_billing_data
ADD
  CONSTRAINT FK_81F7C1193EFDB9F2 FOREIGN KEY (correspondence_country_id) REFERENCES country (country_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_billing_data
ADD
  CONSTRAINT FK_81F7C119EA3721C1 FOREIGN KEY (correspondence_province_id) REFERENCES province (province_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project_archived_directories
ADD
  CONSTRAINT FK_52488189166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C2EB84F83 FOREIGN KEY (system_account_id) REFERENCES system_account (system_account_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C81B2B6EE FOREIGN KEY (address_country_id) REFERENCES country (country_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739CFE893281 FOREIGN KEY (address_province_id) REFERENCES province (province_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739CDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (branch_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C3EFDB9F2 FOREIGN KEY (correspondence_country_id) REFERENCES country (country_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739CEA3721C1 FOREIGN KEY (correspondence_province_id) REFERENCES province (province_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C3264F7 FOREIGN KEY (default_payment_conditions_id) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C30A31E38 FOREIGN KEY (default_payment_conditions_id_for_empty_invoice) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739CC9F1E59 FOREIGN KEY (lead_source_id) REFERENCES lead_source (lead_source_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C43897540 FOREIGN KEY (vat_rate_id) REFERENCES vat_rate (vat_rate_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C3094BE35 FOREIGN KEY (accountency_contact_person_id) REFERENCES provider_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider
ADD
  CONSTRAINT FK_92C4739C666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_related_providers
ADD
  CONSTRAINT FK_89AC2E18A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE feedback_related_providers
ADD
  CONSTRAINT FK_89AC2E18D249A887 FOREIGN KEY (feedback_id) REFERENCES feedback (feedback_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_categories
ADD
  CONSTRAINT FK_1F450AD0A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_categories
ADD
  CONSTRAINT FK_1F450AD0DA896A19 FOREIGN KEY (project_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_invoice
ADD
  CONSTRAINT FK_603DB5E338248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_invoice
ADD
  CONSTRAINT FK_603DB5E338FFD5C2 FOREIGN KEY (payment_conditions_id) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_invoice
ADD
  CONSTRAINT FK_603DB5E348689252 FOREIGN KEY (accountency_person_id) REFERENCES provider_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_invoice
ADD
  CONSTRAINT FK_603DB5E3A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_invoice_categories
ADD
  CONSTRAINT FK_8025B4F5C3FBD45 FOREIGN KEY (provider_invoice_id) REFERENCES provider_invoice (provider_invoice_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_invoice_categories
ADD
  CONSTRAINT FK_8025B4F59C42A4E9 FOREIGN KEY (provider_invoice_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_price_list
ADD
  CONSTRAINT FK_C4530E2038248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_payment_item
ADD
  CONSTRAINT FK_6DEEBED78CB651A6 FOREIGN KEY (provider_charge_id) REFERENCES provider_charge (provider_charge_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_payment_item
ADD
  CONSTRAINT FK_6DEEBED72175EF03 FOREIGN KEY (provider_payment_id) REFERENCES provider_payment (provider_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D73D23D23D FOREIGN KEY (customer_contact_person) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D79395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D71D35E30E FOREIGN KEY (sales_person_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D75EE0192F FOREIGN KEY (first_choosed_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D7497E80EB FOREIGN KEY (optimistic_net_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D7F840DD77 FOREIGN KEY (pessimistic_net_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D7DA0C8AB3 FOREIGN KEY (optimistic_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D7B914703 FOREIGN KEY (pessimistic_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D73F3DB285 FOREIGN KEY (most_probable_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity
ADD
  CONSTRAINT FK_8389C3D7A07B82FB FOREIGN KEY (most_probable_net_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE metrics_progress
ADD
  CONSTRAINT FK_F19B1C193693D6A FOREIGN KEY (metrics_id) REFERENCES metrics (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_price_profile
ADD
  CONSTRAINT FK_8E141250ECD792C0 FOREIGN KEY (default_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_price_profile
ADD
  CONSTRAINT FK_8E1412509395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_price_profile
ADD
  CONSTRAINT FK_8E141250BA0AA1A3 FOREIGN KEY (default_contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_price_profile
ADD
  CONSTRAINT FK_8E1412505688DED7 FOREIGN KEY (price_list_id) REFERENCES customer_price_list (customer_price_list_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_finance
ADD
  CONSTRAINT FK_D1C9C00A8DB60186 FOREIGN KEY (task_id) REFERENCES task (task_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_finance
ADD
  CONSTRAINT FK_D1C9C00A38248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_amount_modifiers
ADD
  CONSTRAINT FK_D1EE559D4294ED6D FOREIGN KEY (task_finance_id) REFERENCES task_finance (task_finance_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_amount_modifiers
ADD
  CONSTRAINT FK_D1EE559D44B38603 FOREIGN KEY (amount_modifier_id) REFERENCES amount_modifier (amount_modifier_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_feedback_answer
ADD
  CONSTRAINT FK_9CB938F1166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_feedback_answer
ADD
  CONSTRAINT FK_9CB938F14A99CC4E FOREIGN KEY (customer_feedback_question_id) REFERENCES customer_feedback_question (customer_feedback_question_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person
ADD
  CONSTRAINT FK_A44EE6F7666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person
ADD
  CONSTRAINT FK_A44EE6F7A60EC612 FOREIGN KEY (person_department_id) REFERENCES person_department (person_department_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person
ADD
  CONSTRAINT FK_A44EE6F7B06F71CE FOREIGN KEY (person_position_id) REFERENCES person_position (person_position_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person
ADD
  CONSTRAINT FK_A44EE6F72EB84F83 FOREIGN KEY (system_account_id) REFERENCES system_account (system_account_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person
ADD
  CONSTRAINT FK_A44EE6F781B2B6EE FOREIGN KEY (address_country_id) REFERENCES country (country_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person
ADD
  CONSTRAINT FK_A44EE6F7FE893281 FOREIGN KEY (address_province_id) REFERENCES province (province_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE person_native_languages
ADD
  CONSTRAINT FK_F73708EA4F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE person_native_languages
ADD
  CONSTRAINT FK_F73708EA82F1BAF4 FOREIGN KEY (language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person_categories2
ADD
  CONSTRAINT FK_C1959C534F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE contact_person_categories2
ADD
  CONSTRAINT FK_C1959C53DA896A19 FOREIGN KEY (project_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_payment
ADD
  CONSTRAINT FK_71F520B38F544A4E FOREIGN KEY (accepted_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_payment
ADD
  CONSTRAINT FK_71F520B3CE3D2663 FOREIGN KEY (received_currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_payment
ADD
  CONSTRAINT FK_71F520B39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_payment
ADD
  CONSTRAINT FK_71F520B35AA1164F FOREIGN KEY (payment_method_id) REFERENCES account (account_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_payment
ADD
  CONSTRAINT FK_71F520B363E40D47 FOREIGN KEY (
    prepayment_clearing_correlated_customer_payment_id
  ) REFERENCES customer_payment (customer_payment_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_minimal_charge
ADD
  CONSTRAINT FK_C91C46788ADF6038 FOREIGN KEY (customer_language_combination_id) REFERENCES customer_language_combination (customer_language_combination_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_minimal_charge
ADD
  CONSTRAINT FK_C91C467868D34FA0 FOREIGN KEY (customer_price_profile_id) REFERENCES customer_price_profile (customer_price_profile_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE tm_savings_item
ADD
  CONSTRAINT FK_5ED6457F393F8EE FOREIGN KEY (tm_savings_id) REFERENCES tm_savings (tm_savings_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE external_system_project
ADD
  CONSTRAINT FK_D2A9B49B9E38855A FOREIGN KEY (activity_with_all_files_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_charge
ADD
  CONSTRAINT FK_E4AE786538248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_charge
ADD
  CONSTRAINT FK_E4AE78659395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_charge
ADD
  CONSTRAINT FK_E4AE7865D440F57F FOREIGN KEY (customer_invoice_id) REFERENCES customer_invoice (customer_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_charge
ADD
  CONSTRAINT FK_E4AE7865166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project_language_combination
ADD
  CONSTRAINT FK_9BAC5CB2166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project_language_combination
ADD
  CONSTRAINT FK_9BAC5CB2BE8EEA54 FOREIGN KEY (source_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project_language_combination
ADD
  CONSTRAINT FK_9BAC5CB25CBF5FE FOREIGN KEY (target_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE workflow_job_phase
ADD
  CONSTRAINT FK_4D48F13E2AE5F46A FOREIGN KEY (workflow_job_id) REFERENCES workflow_job (workflow_job_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E092EB84F83 FOREIGN KEY (system_account_id) REFERENCES system_account (system_account_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E0981B2B6EE FOREIGN KEY (address_country_id) REFERENCES country (country_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09FE893281 FOREIGN KEY (address_province_id) REFERENCES province (province_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (branch_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E093EFDB9F2 FOREIGN KEY (correspondence_country_id) REFERENCES country (country_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09EA3721C1 FOREIGN KEY (correspondence_province_id) REFERENCES province (province_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E093264F7 FOREIGN KEY (default_payment_conditions_id) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E0930A31E38 FOREIGN KEY (default_payment_conditions_id_for_empty_invoice) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09C9F1E59 FOREIGN KEY (lead_source_id) REFERENCES lead_source (lead_source_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E0943897540 FOREIGN KEY (vat_rate_id) REFERENCES vat_rate (vat_rate_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E093094BE35 FOREIGN KEY (accountency_contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E096461135E FOREIGN KEY (in_house_am_responsible_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09D414B7CB FOREIGN KEY (in_house_pc_responsible_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09F4964160 FOREIGN KEY (in_house_pm_responsible_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E0948616623 FOREIGN KEY (in_house_sp_responsible_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09F8B9D183 FOREIGN KEY (parent_customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09F6AE1F70 FOREIGN KEY (xtrf_user_group_id) REFERENCES xtrf_user_group (xtrf_user_group_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer
ADD
  CONSTRAINT FK_81398E09E820FEAE FOREIGN KEY (customer_portal_price_profile) REFERENCES customer_price_profile (customer_price_profile_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_categories
ADD
  CONSTRAINT FK_C73A42EA9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_categories
ADD
  CONSTRAINT FK_C73A42EADA896A19 FOREIGN KEY (project_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_industries
ADD
  CONSTRAINT FK_9BB356099395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_industries
ADD
  CONSTRAINT FK_9BB356092B19A734 FOREIGN KEY (industry_id) REFERENCES industry (industry_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_customer_persons
ADD
  CONSTRAINT FK_AE2031719395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_customer_persons
ADD
  CONSTRAINT FK_AE203171217BBB47 FOREIGN KEY (person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_accountency_contact_persons
ADD
  CONSTRAINT FK_347A972C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_accountency_contact_persons
ADD
  CONSTRAINT FK_347A972C8A86435D FOREIGN KEY (customer_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_aditional_persons_responsible
ADD
  CONSTRAINT FK_5CCF40C87CA9501E FOREIGN KEY (xtrf_user_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_language_specializations
ADD
  CONSTRAINT FK_CD0E3479395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_language_specializations
ADD
  CONSTRAINT FK_CD0E347685ACD4E FOREIGN KEY (language_specialization_id) REFERENCES language_specialization (language_specialization_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_languages
ADD
  CONSTRAINT FK_B2CAB639395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_languages
ADD
  CONSTRAINT FK_B2CAB63CE6064C2 FOREIGN KEY (xtrf_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_charge
ADD
  CONSTRAINT FK_71D4434FDE68D7D FOREIGN KEY (calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_charge
ADD
  CONSTRAINT FK_71D443481C06096 FOREIGN KEY (activity_id) REFERENCES activity (activity_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_language_combination
ADD
  CONSTRAINT FK_6213E028BE8EEA54 FOREIGN KEY (source_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_language_combination
ADD
  CONSTRAINT FK_6213E0285CBF5FE FOREIGN KEY (target_language_id) REFERENCES xtrf_language (xtrf_language_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_language_combination
ADD
  CONSTRAINT FK_6213E0289395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_language_combination_specializations
ADD
  CONSTRAINT FK_D7C55E9B8ADF6038 FOREIGN KEY (customer_language_combination_id) REFERENCES customer_language_combination (customer_language_combination_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_language_combination_specializations
ADD
  CONSTRAINT FK_D7C55E9B685ACD4E FOREIGN KEY (language_specialization_id) REFERENCES language_specialization (language_specialization_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_services
ADD
  CONSTRAINT FK_5FF7A2469395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_services
ADD
  CONSTRAINT FK_5FF7A246ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (service_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_services
ADD
  CONSTRAINT FK_5FF7A2462C7C2CBA FOREIGN KEY (workflow_id) REFERENCES workflow (workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_cat_charge
ADD
  CONSTRAINT FK_977F0AEEFDE68D7D FOREIGN KEY (calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_cat_charge
ADD
  CONSTRAINT FK_977F0AEEF393F8EE FOREIGN KEY (tm_savings_id) REFERENCES tm_savings (tm_savings_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_cat_charge
ADD
  CONSTRAINT FK_977F0AEEC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_cat_charge
ADD
  CONSTRAINT FK_977F0AEE4294ED6D FOREIGN KEY (task_finance_id) REFERENCES task_finance (task_finance_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_cat_charge_amount_modifiers
ADD
  CONSTRAINT FK_46F0F60CFF68BC2A FOREIGN KEY (task_cat_charge_id) REFERENCES task_cat_charge (task_cat_charge_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_cat_charge_amount_modifiers
ADD
  CONSTRAINT FK_46F0F60C44B38603 FOREIGN KEY (amount_modifier_id) REFERENCES amount_modifier (amount_modifier_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE workflow
ADD
  CONSTRAINT FK_65C59816E5F449DF FOREIGN KEY (default_task_workflow_id) REFERENCES workflow (workflow_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_charge
ADD
  CONSTRAINT FK_25A47A491A77836 FOREIGN KEY (charge_type_id) REFERENCES charge_type (charge_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE provider_charge
ADD
  CONSTRAINT FK_25A47A438248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE xtrf_user_group
ADD
  CONSTRAINT FK_77CCD7BC73154ED4 FOREIGN KEY (leader_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE workflow_job_instance
ADD
  CONSTRAINT FK_E62DB4EF2AE5F46A FOREIGN KEY (workflow_job_id) REFERENCES workflow_job (workflow_job_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE workflow_job_instance
ADD
  CONSTRAINT FK_E62DB4EF1BA80DE3 FOREIGN KEY (project_task_id) REFERENCES task (task_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE payment_conditions
ADD
  CONSTRAINT FK_C190887C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE payment_conditions
ADD
  CONSTRAINT FK_C190887CA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (provider_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity_offer
ADD
  CONSTRAINT FK_7142895638248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity_offer
ADD
  CONSTRAINT FK_71428956DB805178 FOREIGN KEY (quote_id) REFERENCES quote (quote_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity_offer
ADD
  CONSTRAINT FK_714289569A34590F FOREIGN KEY (opportunity_id) REFERENCES opportunity (opportunity_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE opportunity_offer
ADD
  CONSTRAINT FK_71428956EF666483 FOREIGN KEY (opportunity_status_id) REFERENCES opportunity_status (opportunity_status_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE84A5C6C7 FOREIGN KEY (account_manager_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE4F8A983C FOREIGN KEY (contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE38248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE68D34FA0 FOREIGN KEY (customer_price_profile_id) REFERENCES customer_price_profile (customer_price_profile_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE685ACD4E FOREIGN KEY (language_specialization_id) REFERENCES language_specialization (language_specialization_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EEE304DE4 FOREIGN KEY (project_coordinator_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE60984F51 FOREIGN KEY (project_manager_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EEB495491C FOREIGN KEY (send_back_to_customer_contact_person_id) REFERENCES customer_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EEDB805178 FOREIGN KEY (quote_id) REFERENCES quote (quote_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EE1D35E30E FOREIGN KEY (sales_person_id) REFERENCES xtrf_user (xtrf_user_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project
ADD
  CONSTRAINT FK_2FB3D0EEED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (service_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project_categories
ADD
  CONSTRAINT FK_22553D5A166D1F9C FOREIGN KEY (project_id) REFERENCES project (project_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE project_categories
ADD
  CONSTRAINT FK_22553D5ADA896A19 FOREIGN KEY (project_category_id) REFERENCES category (category_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095AC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095A4F8A983C FOREIGN KEY (contact_person_id) REFERENCES provider_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095A38248176 FOREIGN KEY (currency_id) REFERENCES xtrf_currency (xtrf_currency_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095A1896582 FOREIGN KEY (workflow_job_instance_id) REFERENCES workflow_job_instance (workflow_job_instance_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095AC3FBD45 FOREIGN KEY (provider_invoice_id) REFERENCES provider_invoice (provider_invoice_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095A38FFD5C2 FOREIGN KEY (payment_conditions_id) REFERENCES payment_conditions (payment_conditions_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095A8DB60186 FOREIGN KEY (task_id) REFERENCES task (task_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095A43897540 FOREIGN KEY (vat_rate_id) REFERENCES vat_rate (vat_rate_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity
ADD
  CONSTRAINT FK_AC74095A666E4E08 FOREIGN KEY (custom_fields_id) REFERENCES custom_fields (custom_fields_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_amount_modifiers
ADD
  CONSTRAINT FK_A554DCF981C06096 FOREIGN KEY (activity_id) REFERENCES activity (activity_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE activity_amount_modifiers
ADD
  CONSTRAINT FK_A554DCF944B38603 FOREIGN KEY (amount_modifier_id) REFERENCES amount_modifier (amount_modifier_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE tm_rates_item
ADD
  CONSTRAINT FK_6918F5894ED1E4FC FOREIGN KEY (tm_rates_id) REFERENCES tm_rates (tm_rates_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_person
ADD
  CONSTRAINT FK_85190D274F8A983C FOREIGN KEY (contact_person_id) REFERENCES contact_person (contact_person_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_person
ADD
  CONSTRAINT FK_85190D279395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (customer_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE customer_person
ADD
  CONSTRAINT FK_85190D27F6AE1F70 FOREIGN KEY (xtrf_user_group_id) REFERENCES xtrf_user_group (xtrf_user_group_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_charge
ADD
  CONSTRAINT FK_6EC220E7FDE68D7D FOREIGN KEY (calculation_unit_id) REFERENCES calculation_unit (calculation_unit_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_charge
ADD
  CONSTRAINT FK_6EC220E7C51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (activity_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_charge
ADD
  CONSTRAINT FK_6EC220E74294ED6D FOREIGN KEY (task_finance_id) REFERENCES task_finance (task_finance_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_charge_amount_modifiers
ADD
  CONSTRAINT FK_7DAE33012A028445 FOREIGN KEY (task_charge_id) REFERENCES task_charge (task_charge_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE task_charge_amount_modifiers
ADD
  CONSTRAINT FK_7DAE330144B38603 FOREIGN KEY (amount_modifier_id) REFERENCES amount_modifier (amount_modifier_id) NOT DEFERRABLE INITIALLY IMMEDIATE;
