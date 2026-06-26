<?php
/**
 * Block template for CB Impact Diagram 1.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$title = get_field( 'title' );
$row_1 = get_field( 'row_1' );

$row_2_col_1_title   = get_field( 'row_2_col_1_title' );
$row_2_col_1_content = get_field( 'row_2_col_1_content' );
$row_2_col_2_title   = get_field( 'row_2_col_2_title' );
$row_2_col_2_content = get_field( 'row_2_col_2_content' );

$row_3_col_1_title   = get_field( 'row_3_col_1_title' );
$row_3_col_1_content = get_field( 'row_3_col_1_content' );
$row_3_col_2_title   = get_field( 'row_3_col_2_title' );
$row_3_col_2_content = get_field( 'row_3_col_2_content' );

$row_4_col_1_title   = get_field( 'row_4_col_1_title' );
$row_4_col_1_content = get_field( 'row_4_col_1_content' );
$row_4_col_2_title   = get_field( 'row_4_col_2_title' );
$row_4_col_2_content = get_field( 'row_4_col_2_content' );
$row_4_icon          = get_field( 'row_4_icon' );

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-impact-diagram-1-' );
$section_classes = array( 'cb-impact-diagram-1' );

if ( ! empty( $block['className'] ) ) {
    $section_classes[] = $block['className'];
}

$has_row_2 = ! empty( $row_2_col_1_title ) || ! empty( $row_2_col_1_content ) || ! empty( $row_2_col_2_title ) || ! empty( $row_2_col_2_content );
$has_row_3 = ! empty( $row_3_col_1_title ) || ! empty( $row_3_col_1_content ) || ! empty( $row_3_col_2_title ) || ! empty( $row_3_col_2_content );
$has_row_4 = ! empty( $row_4_col_1_title ) || ! empty( $row_4_col_1_content ) || ! empty( $row_4_col_2_title ) || ! empty( $row_4_col_2_content );
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
    <div class="container">
        <?php if ( $title ) { ?>
        <div class="cb-impact-diagram-1__title-wrap">
            <div class="cb-impact-diagram-1__title-line" aria-hidden="true"></div>
            <h2 class="cb-impact-diagram-1__title"><?= esc_html( $title ); ?></h2>
        </div>
        <?php } ?>

        <?php if ( ! empty( $row_1 ) && is_array( $row_1 ) ) { ?>
        <div class="row g-4 cb-impact-diagram-1__row" data-aos-row="1">
            <?php
            foreach ( $row_1 as $r1 ) {
                $icon        = $r1['icon'] ?? null;
                $stat_prefix = $r1['stat_prefix'] ?? '';
                $stat        = $r1['stat'] ?? '';
                $stat_suffix = $r1['stat_suffix'] ?? '';
                $content     = $r1['content'] ?? '';
                ?>
            <div class="col-md-3 cb-impact-diagram-1__card-wrap" data-cid-row="1">
                <div class="cb-impact-diagram-1__card">
                <?php
                if ( $icon ) {
                    $icon_url  = wp_get_attachment_image_url( $icon, 'medium' );
                    $icon_path = get_attached_file( $icon );
                    ?>
                    <div class="cb-impact-diagram-1__card-icon">
                    <?php
                    if ( $icon_path && preg_match( '/\.svg$/i', $icon_path ) && file_exists( $icon_path ) ) {
                        ?>
                        <div class="cb-impact-diagram-1__card-icon-svg"><?= file_get_contents( $icon_path ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                        <?php
                    } else {
                        echo wp_get_attachment_image( $icon, 'medium' );
                    }
                    ?>
                    </div>
                    <?php
                }
                ?>
                    <div class="cb-impact-diagram-1__card-stat">
                <?php
                if ( $stat_prefix ) {
					?>
                    <span class="cb-impact-diagram-1__card-stat-prefix"><?= esc_html( $stat_prefix ); ?></span>
					<?php
                }
                if ( $stat ) {
					?>
                    <span class="cb-impact-diagram-1__card-stat-number" data-stat-target="<?= esc_attr( $stat ); ?>">0</span>
					<?php
                }
                if ( $stat_suffix ) {
					?>
                    <span class="cb-impact-diagram-1__card-stat-suffix"><?= esc_html( $stat_suffix ); ?></span>
					<?php
                }
                ?>
                    </div>
                <?php
                if ( $content ) {
                    ?>
                    <div class="cb-impact-diagram-1__card-text"><?= nl2br( esc_html( $content ) ); ?></div>
                    <?php
                }
                ?>
                </div>
            </div>
                <?php
            }
            ?>
        </div>
            <?php
        }
        ?>

        <div class="cb-impact-diagram-1__section-label" data-cid-label>
            <h3 class="cb-impact-diagram-1__section-label-text">Overview impact performance</h3>
        </div>

        <?php
        if ( $has_row_2 ) {
            ?>
        <div class="row g-4 cb-impact-diagram-1__row" data-aos-row="2">
            <?php
            if ( $row_2_col_1_title || $row_2_col_1_content ) {
                ?>
            <div class="col-md-6 cb-impact-diagram-1__card-wrap" data-cid-row="2">
                <div class="cb-impact-diagram-1__card">
                <?php
                if ( $row_2_col_1_title ) {
                    ?>
                    <h3 class="cb-impact-diagram-1__card-title"><?= esc_html( $row_2_col_1_title ); ?></h3>
                    <?php
                }
                if ( $row_2_col_1_content ) {
                    ?>
                    <ul class="cb-impact-diagram-1__card-list"><?= wp_kses_post( cb_list( $row_2_col_1_content ) ); ?></ul>
                    <?php
                }
                ?>
                </div>
                <div class="cb-impact-diagram-1__card-arrow" aria-hidden="true"></div>
            </div>
                <?php
            }
            if ( $row_2_col_2_title || $row_2_col_2_content ) {
                ?>
            <div class="col-md-6 cb-impact-diagram-1__card-wrap" data-cid-row="2">
                <div class="cb-impact-diagram-1__card">
                <?php
                if ( $row_2_col_2_title ) {
                    ?>
                    <h3 class="cb-impact-diagram-1__card-title"><?= esc_html( $row_2_col_2_title ); ?></h3>
                    <?php
                }
                if ( $row_2_col_2_content ) {
                    ?>
                    <ul class="cb-impact-diagram-1__card-list"><?= wp_kses_post( cb_list( $row_2_col_2_content ) ); ?></ul>
                    <?php
                }
                ?>
                </div>
                <div class="cb-impact-diagram-1__card-arrow" aria-hidden="true"></div>
            </div>
                <?php
            }
            ?>
        </div>
            <?php
        }

        if ( $has_row_3 ) {
            ?>
        <div class="row g-4 cb-impact-diagram-1__row" data-aos-row="3">
            <?php
            if ( $row_3_col_1_title || $row_3_col_1_content ) {
                ?>
            <div class="col-md-6 cb-impact-diagram-1__card-wrap" data-cid-row="3">
                <div class="cb-impact-diagram-1__card">
                <?php
                if ( $row_3_col_1_title ) {
                    ?>
                    <h3 class="cb-impact-diagram-1__card-title"><?= esc_html( $row_3_col_1_title ); ?></h3>
                    <?php
                }
                if ( $row_3_col_1_content ) {
                    ?>
                    <ul class="cb-impact-diagram-1__card-list"><?= wp_kses_post( cb_list( $row_3_col_1_content ) ); ?></ul>
                    <?php
                }
                ?>
                </div>
                <div class="cb-impact-diagram-1__card-arrow" aria-hidden="true"></div>
            </div>
                <?php
            }
            if ( $row_3_col_2_title || $row_3_col_2_content ) {
                ?>
            <div class="col-md-6 cb-impact-diagram-1__card-wrap" data-cid-row="3">
                <div class="cb-impact-diagram-1__card">
                <?php
                if ( $row_3_col_2_title ) {
                    ?>
                    <h3 class="cb-impact-diagram-1__card-title"><?= esc_html( $row_3_col_2_title ); ?></h3>
                    <?php
                }
                if ( $row_3_col_2_content ) {
                    ?>
                    <ul class="cb-impact-diagram-1__card-list"><?= wp_kses_post( cb_list( $row_3_col_2_content ) ); ?></ul>
                    <?php
                }
                ?>
                </div>
                <div class="cb-impact-diagram-1__card-arrow" aria-hidden="true"></div>
            </div>
                <?php
            }
            ?>
        </div>
            <?php
        }

        if ( $has_row_4 ) {
            ?>
        <div class="row g-4 cb-impact-diagram-1__row" data-aos-row="4">
            <div class="col-12 cb-impact-diagram-1__card-wrap" data-cid-row="4">
                <div class="cb-impact-diagram-1__row-4-inner">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-5">
            <?php
            if ( $row_4_col_1_title ) {
                ?>
                            <h3 class="cb-impact-diagram-1__card-title"><?= esc_html( $row_4_col_1_title ); ?></h3>
                <?php
            }
            if ( $row_4_col_1_content ) {
                ?>
                            <ul class="cb-impact-diagram-1__card-list"><?= wp_kses_post( cb_list( $row_4_col_1_content ) ); ?></ul>
                <?php
            }
            ?>
                        </div>
                        <div class="col-md-2 text-center">
            <?php
            if ( $row_4_icon ) {
                $r4_icon_path = get_attached_file( $row_4_icon );
                ?>
                            <div class="cb-impact-diagram-1__rotating-icon">
                <?php
                if ( $r4_icon_path && preg_match( '/\.svg$/i', $r4_icon_path ) && file_exists( $r4_icon_path ) ) {
                    ?>
                                <div class="cb-impact-diagram-1__card-icon-svg"><?= file_get_contents( $r4_icon_path ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                    <?php
                } else {
                    ?>
                                <?= wp_get_attachment_image( $row_4_icon, 'medium' ); ?>
                    <?php
                }
                ?>
                            </div>
                <?php
            } else {
                ?>
                            <div class="cb-impact-diagram-1__rotating-icon cb-impact-diagram-1__rotating-icon--placeholder">
                                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="6"/>
                                    <path d="M50 10 A40 40 0 0 1 90 50" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                                </svg>
                            </div>
                <?php
            }
            ?>
                        </div>
                        <div class="col-md-5">
            <?php
            if ( $row_4_col_2_title ) {
                ?>
                            <h3 class="cb-impact-diagram-1__card-title"><?= esc_html( $row_4_col_2_title ); ?></h3>
                <?php
            }
            if ( $row_4_col_2_content ) {
                ?>
                            <ul class="cb-impact-diagram-1__card-list"><?= wp_kses_post( cb_list( $row_4_col_2_content ) ); ?></ul>
                <?php
            }
            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php
        }
        ?>
    </div>
</section>
<script>
(() => {
    const root = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
    if (!root) return;

    const init = () => {
        if (!window.gsap) return false;

        const wraps = root.querySelectorAll('.cb-impact-diagram-1__card-wrap');
        if (!wraps.length) return false;

        const label = root.querySelector('[data-cid-label]');

        window.gsap.set(wraps, { autoAlpha: 0, y: 24 });
        if (label) window.gsap.set(label, { autoAlpha: 0, y: 16 });

        const countUp = (el) => {
            const targetRaw = parseFloat(el.getAttribute('data-stat-target') || '0');
            if (!targetRaw) return;
            const decimals = Number.isInteger(targetRaw) ? 0 : 2;
            const formatter = new Intl.NumberFormat(undefined, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });
            const state = { value: 0 };
            window.gsap.to(state, {
                value: targetRaw,
                duration: 1.2,
                ease: 'power2.out',
                onUpdate: () => {
                    el.textContent = formatter.format(state.value);
                },
                onComplete: () => {
                    el.textContent = formatter.format(targetRaw);
                },
            });
        };

        const tl = window.gsap.timeline({ paused: true });
        let pos = '>';

        const row1 = root.querySelectorAll('[data-cid-row="1"]');
        if (row1.length) {
            tl.to(row1, {
                autoAlpha: 1,
                y: 0,
                duration: 0.6,
                ease: 'power3.out',
                stagger: {
                    each: 0.18,
                    onComplete: function () {
                        const stat = this.targets()[0].querySelector('.cb-impact-diagram-1__card-stat-number');
                        if (stat) countUp(stat);
                    },
                },
            }, pos);
            pos = '>+=0.2';
        }

        if (label) {
            tl.to(label, {
                autoAlpha: 1,
                y: 0,
                duration: 0.5,
                ease: 'power3.out',
            }, pos);
            pos = '>+=0.2';
        }

        [2, 3, 4].forEach((rowNum) => {
            const cards = root.querySelectorAll('[data-cid-row="' + rowNum + '"]');
            if (cards.length) {
                tl.to(cards, {
                    autoAlpha: 1,
                    y: 0,
                    duration: 0.6,
                    ease: 'power3.out',
                    stagger: {
                        each: 0.18,
                    },
                }, pos);
                pos = '>+=0.2';
            }
        });

        if (window.IntersectionObserver) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        tl.play();
                        observer.disconnect();
                    }
                });
            }, { threshold: 0.15 });
            observer.observe(root);
        } else {
            tl.play();
        }

        return true;
    };

    const waitForGsap = () => {
        if (!init()) {
            window.setTimeout(waitForGsap, 50);
        }
    };

    if (document.readyState === 'complete') {
        waitForGsap();
    } else {
        window.addEventListener('load', waitForGsap, { once: true });
    }
})();
</script>
