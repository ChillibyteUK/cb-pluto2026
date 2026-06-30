<?php
/**
 * Block template for CB Impact Diagram 2.
 *
 * @package cb-pluto2026
 */

defined( 'ABSPATH' ) || exit;

$cards = array(
    1 => array(
        'icon'  => get_field( 'card_1_icon' ),
        'title' => get_field( 'card_1_title' ),
        'items' => get_field( 'card_1_items' ),
    ),
    2 => array(
        'icon'  => get_field( 'card_2_icon' ),
        'title' => get_field( 'card_2_title' ),
        'items' => get_field( 'card_2_items' ),
    ),
    3 => array(
        'icon'  => get_field( 'card_3_icon' ),
        'title' => get_field( 'card_3_title' ),
        'items' => get_field( 'card_3_items' ),
    ),
);

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-impact-diagram-2-' );
$section_classes = array( 'cb-impact-diagram-2' );

if ( ! empty( $block['className'] ) ) {
    $section_classes[] = $block['className'];
}

$has_any_card = false;
foreach ( $cards as $card ) {
    if ( ! empty( $card['icon'] ) || ! empty( $card['title'] ) || ( ! empty( $card['items'] ) && is_array( $card['items'] ) ) ) {
        $has_any_card = true;
        break;
    }
}

if ( ! $has_any_card ) {
    return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
    <div class="container">
        <div class="row g-4">
            <?php
			foreach ( $cards as $card_num => $card ) {
	            $show_card = ! empty( $card['icon'] ) || ! empty( $card['title'] ) || ( ! empty( $card['items'] ) && is_array( $card['items'] ) );
    	        if ( ! $show_card ) {
        	        continue;
	            }
    	        ?>
            <div class="col-md-4 cb-impact-diagram-2__card-wrap" data-cid2-card="<?= esc_attr( $card_num ); ?>">
                <div class="cb-impact-diagram-2__card">
                    <div class="cb-impact-diagram-2__card-header">
                        <?php
                        if ( $card['icon'] ) {
                            $icon_path = get_attached_file( $card['icon'] );
                            ?>
                            <div class="cb-impact-diagram-2__card-icon">
                            <?php
                            if ( $icon_path && preg_match( '/\.svg$/i', $icon_path ) && file_exists( $icon_path ) ) {
                                ?>
                                <div class="cb-impact-diagram-2__card-icon-svg"><?= file_get_contents( $icon_path ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                <?php
                            } else {
                                echo wp_get_attachment_image( $card['icon'], 'medium' );
                            }
                            ?>
                            </div>
                            <?php
                        }
                        if ( $card['title'] ) {
                            ?>
                            <h3 class="cb-impact-diagram-2__card-title"><?= esc_html( $card['title'] ); ?></h3>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
					if ( ! empty( $card['items'] ) && is_array( $card['items'] ) ) {
						?>
                    <div class="cb-impact-diagram-2__card-items" data-cid2-items="<?= esc_attr( $card_num ); ?>">
                        <?php
						foreach ( $card['items'] as $index => $item ) {
							?>
                        <div class="cb-impact-diagram-2__card-item" data-cid2-row="<?= esc_attr( $card_num . '-' . $index ); ?>">
                            <div class="cb-impact-diagram-2__card-stat">
                                <?php
								if ( ! empty( $item['stat_prefix'] ) ) {
									?>
                                <span class="cb-impact-diagram-2__card-stat-prefix"><?= esc_html( $item['stat_prefix'] ); ?></span>
                                	<?php
								}
								if ( isset( $item['stat'] ) && '' !== $item['stat'] ) {
									?>
                                <span class="cb-impact-diagram-2__card-stat-number" data-stat-target="<?= esc_attr( $item['stat'] ); ?>">0</span>
                                	<?php
								}
								if ( ! empty( $item['stat_suffix'] ) ) {
									?>
                                <span class="cb-impact-diagram-2__card-stat-suffix"><?= esc_html( $item['stat_suffix'] ); ?></span>
                                	<?php
								}
								?>
                            </div>
                            	<?php
								if ( ! empty( $item['item'] ) ) {
									?>
                            <div class="cb-impact-diagram-2__card-item-text"><?= esc_html( $item['item'] ); ?></div>
                            		<?php
								}
								?>
                        </div>
                        	<?php
						}
						?>
                    </div>
                    	<?php
					}
					?>
                </div>
            </div>
            	<?php
			}
			?>
        </div>
    </div>
</section>
<script>
(() => {
    const root = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
    if (!root) return;

    const init = () => {
        if (!window.gsap) return false;

        const cards = root.querySelectorAll('[data-cid2-card]');
        if (!cards.length) return false;

        window.gsap.set(cards, { autoAlpha: 0, y: 24 });

        const itemRows = root.querySelectorAll('[data-cid2-row]');
        window.gsap.set(itemRows, { autoAlpha: 0, y: 12 });

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

        tl.to(cards, {
            autoAlpha: 1,
            y: 0,
            duration: 0.6,
            ease: 'power3.out',
            stagger: {
                each: 0.2,
                onComplete: function () {
                    const cardEl = this.targets()[0];
                    const cardNum = cardEl.getAttribute('data-cid2-card');
                    const rows = root.querySelectorAll('[data-cid2-row^="' + cardNum + '-"]');
                    if (rows.length) {
                        window.gsap.to(rows, {
                            autoAlpha: 1,
                            y: 0,
                            duration: 0.5,
                            ease: 'power3.out',
                            stagger: {
                                each: 0.15,
                                onComplete: function () {
                                    const stat = this.targets()[0].querySelector('.cb-impact-diagram-2__card-stat-number');
                                    if (stat) countUp(stat);
                                },
                            },
                        });
                    }
                },
            },
        });

        const play = () => { tl.play(); };

        const diagram1 = document.querySelector('.cb-impact-diagram-1');
        if (diagram1) {
            let started = false;
            const onEvent = () => {
                if (started) return;
                started = true;
                document.removeEventListener('cbImpactDiagram1Complete', onEvent);
                play();
            };
            document.addEventListener('cbImpactDiagram1Complete', onEvent);
            window.setTimeout(onEvent, 10000);
        } else if (window.IntersectionObserver) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        observer.disconnect();
                        play();
                    }
                });
            }, { threshold: 0.15 });
            observer.observe(root);
        } else {
            play();
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
