<?php ob_start(); ?>

<style>
.pending-container {
    max-width: 500px;
    margin: 2rem auto;
    padding: 0 1rem;
}
.pending-card {
    background: var(--white);
    border-radius: 20px;
    padding: 2.5rem;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.pending-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}
.pending-icon i {
    font-size: 2.5rem;
    color: white;
}
.pending-card h1 {
    margin: 0 0 0.75rem 0;
    font-size: 1.5rem;
    color: var(--text);
}
.pending-card p {
    margin: 0 0 1.5rem 0;
    color: var(--text-light);
    line-height: 1.6;
}
.business-name {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--secondary);
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    font-weight: 600;
    color: var(--text);
}
.business-name i {
    color: var(--primary);
}
.info-box {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    text-align: left;
}
.info-box p {
    margin: 0;
    font-size: 0.9rem;
    color: #92400e;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}
.info-box i {
    margin-top: 2px;
    flex-shrink: 0;
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary);
    color: white;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: transform 0.2s;
}
.btn-back:hover {
    transform: translateY(-2px);
}
</style>

<div class="pending-container">
    <div class="pending-card">
        <div class="pending-icon">
            <i class="fas fa-clock"></i>
        </div>

        <div class="business-name">
            <i class="fas fa-store"></i>
            <?= htmlspecialchars($business['name']) ?>
        </div>

        <h1>Nog niet beschikbaar</h1>
        <p>
            Dit bedrijf is recentelijk geregistreerd en wordt momenteel geverifieerd door ons team.
        </p>

        <div class="info-box">
            <p>
                <i class="fas fa-info-circle"></i>
                De verificatie wordt binnen 24 uur afgerond. Probeer het later opnieuw om een afspraak te boeken.
            </p>
        </div>

        <a href="/business/<?= htmlspecialchars($business['slug']) ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> Terug naar bedrijfspagina
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
