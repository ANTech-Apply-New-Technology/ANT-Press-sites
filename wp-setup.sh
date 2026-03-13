#!/usr/bin/env bash
set -euo pipefail

a2dismod mpm_event 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

(
    MAX_TRIES=60
    TRIES=0
    until curl -sf http://localhost/ > /dev/null 2>&1; do
        TRIES=$((TRIES + 1))
        if [ "$TRIES" -ge "$MAX_TRIES" ]; then
            echo "WP-SETUP: WordPress not ready after $MAX_TRIES attempts" >&2
            exit 1
        fi
        sleep 3
    done

    echo "WP-SETUP: WordPress is responding, running setup..."

    WP_URL="${WP_HOME:-http://localhost}"
    WP_TITLE="${WP_SITE_TITLE:-ANT-Press Site}"
    WP_ADMIN="${WP_ADMIN_USER:-admin}"
    WP_PASS="${WP_ADMIN_PASS:-$(head -c 24 /dev/urandom | base64)}"
    WP_EMAIL="${WP_ADMIN_EMAIL:-admin@antpress.dev}"

    if ! wp core is-installed --path=/var/www/html --allow-root 2>/dev/null; then

        wp core install \
            --path=/var/www/html \
            --url="$WP_URL" \
            --title="$WP_TITLE" \
            --admin_user="$WP_ADMIN" \
            --admin_password="$WP_PASS" \
            --admin_email="$WP_EMAIL" \
            --skip-email \
            --allow-root

        wp rewrite structure '/%postname%/' --path=/var/www/html --allow-root
        wp rewrite flush --path=/var/www/html --allow-root

        mkdir -p /var/www/html/wp-content/mu-plugins
        printf '%s\n' '<?php' 'add_filter("wp_is_application_passwords_available", "__return_true");' > /var/www/html/wp-content/mu-plugins/force-app-passwords.php

        # Activate Makiro theme
        if [ -d /var/www/html/wp-content/themes/makiro-theme ]; then
            echo "WP-SETUP: Activating Makiro theme..."
            wp theme activate makiro-theme --path=/var/www/html --allow-root
        fi

        APP_PASSWORD=$(wp user application-password create "$WP_ADMIN" "ant-press-api" \
            --path=/var/www/html --porcelain --allow-root 2>/dev/null || echo "")

        echo "=========================================="
        echo "ANT-PRESS SETUP COMPLETE"
        echo "URL: $WP_URL"
        echo "Admin: $WP_ADMIN / $WP_PASS"
        echo "API App Password: $APP_PASSWORD"
        echo "=========================================="
    else
        echo "WP-SETUP: WordPress already installed, skipping core install."
        # Still activate makiro if not active
        if [ -d /var/www/html/wp-content/themes/makiro-theme ]; then
            CURRENT=$(wp theme list --status=active --field=name --path=/var/www/html --allow-root 2>/dev/null || echo "")
            if [ "$CURRENT" != "makiro-theme" ]; then
                echo "WP-SETUP: Activating Makiro theme..."
                wp theme activate makiro-theme --path=/var/www/html --allow-root
            fi
        fi
    fi

    # Set JAMA Maskin theme customizer values
    echo "WP-SETUP: Applying JAMA Maskin customizer settings..."
    THEME_MODS_SET=0
    EXISTING=$(wp theme mod get makiro_hero_badge --path=/var/www/html --allow-root 2>/dev/null || echo "")
    if [ "$EXISTING" != "Bygg & Markanläggning" ]; then
wp theme mod set makiro_hero_badge 'Bygg & Markanläggning' --path=/var/www/html --allow-root
wp theme mod set makiro_hero_heading_1 'JAMA Maskin' --path=/var/www/html --allow-root
wp theme mod set makiro_hero_heading_2 'kvalitet' --path=/var/www/html --allow-root
wp theme mod set makiro_hero_heading_2_prefix 'Bygger med ' --path=/var/www/html --allow-root
wp theme mod set makiro_hero_description 'Professionella bygg- och markanläggningsarbeten med moderna maskiner och lång erfarenhet. Från schaktning och grundläggning till markplanering och maskinuthyrning.' --path=/var/www/html --allow-root
wp theme mod set makiro_hero_btn_primary 'Begär offert' --path=/var/www/html --allow-root
wp theme mod set makiro_hero_btn_secondary 'Våra tjänster' --path=/var/www/html --allow-root
wp theme mod set makiro_stat_1_value '25+' --path=/var/www/html --allow-root
wp theme mod set makiro_stat_1_label 'Års erfarenhet' --path=/var/www/html --allow-root
wp theme mod set makiro_stat_2_value '100%' --path=/var/www/html --allow-root
wp theme mod set makiro_stat_2_label 'Nöjda kunder' --path=/var/www/html --allow-root
wp theme mod set makiro_stat_3_value '24h' --path=/var/www/html --allow-root
wp theme mod set makiro_stat_3_label 'Offert inom' --path=/var/www/html --allow-root
wp theme mod set makiro_float_top_label 'Just nu' --path=/var/www/html --allow-root
wp theme mod set makiro_float_top_value 'Lediga maskiner' --path=/var/www/html --allow-root
wp theme mod set makiro_float_bottom_label 'Populärt' --path=/var/www/html --allow-root
wp theme mod set makiro_float_bottom_value 'Totalentreprenad' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_1 'Kostnadsfri offert' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_2 'Moderna maskiner' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_3 'Erfarna förare' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_4 'Punktlig leverans' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_5 'Lokal förankring' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_6 'Totalentreprenad' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_7 'Miljöcertifierade' --path=/var/www/html --allow-root
wp theme mod set makiro_trust_8 'Alla typer av mark & bygg' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_title 'Våra tjänsteområden' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_subtitle 'Från markarbeten och grundläggning till färdiga byggnationer — vi tar hand om hela projektet.' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_1_name 'Markarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_1_count 'Schakt & VA' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_2_name 'Grundläggning' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_2_count 'Alla typer' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_3_name 'Byggarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_cat_3_count 'Nybygg & renovering' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_title 'Våra tjänster i detalj' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_1_name 'Schaktning & masshantering' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_1_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_1_cat 'Markarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_1_badge_type '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_1_badge_text '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_1_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_1_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_2_name 'VA-arbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_2_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_2_cat 'Markarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_2_badge_type '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_2_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_2_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_3_name 'Dränering & dagvatten' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_3_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_3_cat 'Markarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_3_badge_type '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_3_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_3_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_4_name 'Platta på mark' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_4_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_4_cat 'Grundläggning' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_4_badge_type '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_4_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_4_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_5_name 'Sprängning' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_5_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_5_cat 'Markarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_5_badge_type '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_5_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_5_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_6_name 'Asfaltering & stenläggning' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_6_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_6_cat 'Ytarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_6_badge_type '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_6_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_6_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_7_name 'Grävmaskin 1,5-25 ton' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_7_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_7_cat 'Maskinuthyrning' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_7_badge_type '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_7_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_7_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_8_name 'Totalentreprenad' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_8_price '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_8_cat 'Helhetslösning' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_8_badge_type 'popular' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_8_badge_text 'Populärt' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_8_reviews '' --path=/var/www/html --allow-root
wp theme mod set makiro_prod_8_stars '5' --path=/var/www/html --allow-root
wp theme mod set makiro_process_title 'Så jobbar vi — från kontakt till färdigt projekt' --path=/var/www/html --allow-root
wp theme mod set makiro_process_subtitle 'Vi tar hand om hela processen med tydlig kommunikation och dokumentation i varje steg.' --path=/var/www/html --allow-root
wp theme mod set makiro_step_1_title 'Kontakt & besiktning' --path=/var/www/html --allow-root
wp theme mod set makiro_step_1_desc 'Ring eller mejla oss. Vi kommer ut för en kostnadsfri besiktning och bedömer ditt projekt på plats.' --path=/var/www/html --allow-root
wp theme mod set makiro_step_2_title 'Offert & planering' --path=/var/www/html --allow-root
wp theme mod set makiro_step_2_desc 'Du får en detaljerad offert inom 24 timmar. Vi planerar tidslinjen och säkerställer att rätt maskiner finns på plats.' --path=/var/www/html --allow-root
wp theme mod set makiro_step_3_title 'Genomförande' --path=/var/www/html --allow-root
wp theme mod set makiro_step_3_desc 'Våra erfarna förare och hantverkare utför arbetet enligt plan med moderna maskiner och hög säkerhet.' --path=/var/www/html --allow-root
wp theme mod set makiro_step_4_title 'Slutbesiktning' --path=/var/www/html --allow-root
wp theme mod set makiro_step_4_desc 'Vi slutbesiktar tillsammans och säkerställer att allt är utfört enligt avtal och gällande byggnormer.' --path=/var/www/html --allow-root
wp theme mod set makiro_viewer_title 'Modern maskinpark' --path=/var/www/html --allow-root
wp theme mod set makiro_viewer_subtitle 'Vi investerar kontinuerligt i moderna, driftsäkra maskiner. Alla uppfyller gällande miljö- och säkerhetskrav.' --path=/var/www/html --allow-root
wp theme mod set makiro_vf_1_title 'Grävmaskin 1,5-25 ton' --path=/var/www/html --allow-root
wp theme mod set makiro_vf_1_desc 'Allt från minigrävare till tunga maskiner' --path=/var/www/html --allow-root
wp theme mod set makiro_vf_2_title 'Hjullastare & dumper' --path=/var/www/html --allow-root
wp theme mod set makiro_vf_2_desc 'Effektiv masshantering och transport' --path=/var/www/html --allow-root
wp theme mod set makiro_vf_3_title 'Specialutrustning' --path=/var/www/html --allow-root
wp theme mod set makiro_vf_3_desc 'Vibroplatta, bergborr och tillbehör efter behov' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_title 'Projekt vi genomfört' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_subtitle 'Ett urval av våra senaste bygg- och markanläggningsprojekt i regionen.' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_1_title 'Villatomt — markarbeten' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_1_desc 'Komplett schaktning och VA-anslutning' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_2_title 'Grundläggning' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_2_desc 'Platta på mark för nybygge' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_3_title 'Dränering' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_3_desc 'Omdränering av äldre fastighet' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_4_title 'Uppfart & parkering' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_4_desc 'Asfaltering och stenläggning' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_5_title 'Totalentreprenad' --path=/var/www/html --allow-root
wp theme mod set makiro_gallery_5_desc 'Från råmark till inflyttningsklart' --path=/var/www/html --allow-root
wp theme mod set makiro_testimonials_label 'Kundomdömen' --path=/var/www/html --allow-root
wp theme mod set makiro_testimonials_title 'Vad våra kunder säger' --path=/var/www/html --allow-root
wp theme mod set makiro_review_1_text 'JAMA Maskin skötte hela markarbetet för vår nya villa. Professionellt, punktligt och rent på arbetsplatsen. Kan varmt rekommendera!' --path=/var/www/html --allow-root
wp theme mod set makiro_review_1_name 'Erik Johansson' --path=/var/www/html --allow-root
wp theme mod set makiro_review_1_role 'Villaägare' --path=/var/www/html --allow-root
wp theme mod set makiro_review_2_text 'Vi anlitade JAMA för dränering och VA-arbeten. De höll tidsplanen till punkt och pricka och kvaliteten var utmärkt.' --path=/var/www/html --allow-root
wp theme mod set makiro_review_2_name 'Maria Andersson' --path=/var/www/html --allow-root
wp theme mod set makiro_review_2_role 'Fastighetsförvaltare' --path=/var/www/html --allow-root
wp theme mod set makiro_review_3_text 'Bästa maskinentreprenören vi jobbat med. Kompetenta förare, moderna maskiner och alltid tillgängliga för frågor.' --path=/var/www/html --allow-root
wp theme mod set makiro_review_3_name 'Per Svensson' --path=/var/www/html --allow-root
wp theme mod set makiro_review_3_role 'Byggledare' --path=/var/www/html --allow-root
wp theme mod set makiro_nl_heading 'Behöver du hjälp med <span class="text-accent">bygg eller mark</span>?' --path=/var/www/html --allow-root
wp theme mod set makiro_nl_text 'Kontakta oss för en kostnadsfri besiktning och offert. Vi återkommer alltid inom 24 timmar.' --path=/var/www/html --allow-root
wp theme mod set makiro_footer_desc 'JAMA Maskin utför professionella bygg- och markanläggningsarbeten med moderna maskiner och erfarna förare. Kvalitet, säkerhet och punktlighet.' --path=/var/www/html --allow-root
wp theme mod set makiro_footer_email 'info@jamamaskin.se' --path=/var/www/html --allow-root
wp theme mod set makiro_footer_phone 'Kontakta oss' --path=/var/www/html --allow-root
wp theme mod set makiro_footer_address1 'Kontakta oss för adress' --path=/var/www/html --allow-root
wp theme mod set makiro_footer_address2 '' --path=/var/www/html --allow-root
        wp theme mod set makiro_hero_bg_image 'https://images.unsplash.com/photo-1581094288338-2314dddb7ece?w=1920&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_hero_product_image 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_cat_1_image 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=800&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_cat_2_image 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_cat_3_image 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=800&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_gallery_1_image 'https://images.unsplash.com/photo-1581094288338-2314dddb7ece?w=900&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_gallery_2_image 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_gallery_3_image 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_gallery_4_image 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_gallery_5_image 'https://images.unsplash.com/photo-1590846083693-f23fdede3a7e?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_1_image 'https://images.unsplash.com/photo-1581094288338-2314dddb7ece?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_2_image 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_3_image 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_4_image 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_5_image 'https://images.unsplash.com/photo-1590846083693-f23fdede3a7e?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_6_image 'https://images.unsplash.com/photo-1517089596392-fb9a9033e05b?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_7_image 'https://images.unsplash.com/photo-1580901368919-7738efb0f228?w=600&q=80' --path=/var/www/html --allow-root
        wp theme mod set makiro_prod_8_image 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=600&q=80' --path=/var/www/html --allow-root
        THEME_MODS_SET=1
        echo "WP-SETUP: Theme mods applied ($THEME_MODS_SET)"
    else
        echo "WP-SETUP: Theme mods already set, skipping."
    fi

) &

exec docker-entrypoint.sh apache2-foreground
