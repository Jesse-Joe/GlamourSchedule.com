<?php ob_start(); ?>

<style>
    .contact-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .contact-card {
        background: var(--white);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .contact-header {
        background: linear-gradient(135deg, #000000 0%, #000000 30%, #000000 70%, #333333 100%);
        color: white;
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .contact-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }
    .contact-header p {
        margin-top: 0.5rem;
        opacity: 0.9;
    }
    .contact-body {
        padding: 2.5rem;
    }
    .contact-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .info-card {
        background: #fafafa;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .info-card i {
        font-size: 2rem;
        color: #000000;
        margin-bottom: 1rem;
    }
    .info-card h3 {
        color: #374151;
        margin: 0 0 0.5rem 0;
        font-size: 1rem;
    }
    .info-card p {
        color: #6b7280;
        margin: 0;
        font-size: 0.95rem;
    }
    .info-card a {
        color: #000000;
        text-decoration: none;
    }
    .info-card a:hover {
        text-decoration: underline;
    }
    .faq-section {
        margin-top: 2rem;
    }
    .faq-section h2 {
        color: #374151;
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }
    .faq-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 0.75rem;
        overflow: hidden;
    }
    .faq-question {
        background: #fafafa;
        padding: 1rem 1.25rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 500;
        color: #374151;
        transition: background 0.2s;
    }
    .faq-question:hover {
        background: #f5f5f5;
    }
    .faq-question i {
        color: #000000;
        transition: transform 0.3s;
    }
    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }
    .faq-answer {
        display: none;
        padding: 1rem 1.25rem;
        color: #6b7280;
        line-height: 1.6;
        border-top: 1px solid #e5e7eb;
    }
    .faq-item.active .faq-answer {
        display: block;
    }
    .social-links {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    .social-link {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
        border-radius: 50%;
        color: #6b7280;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    .social-link:hover {
        background: #000000;
        color: white;
        transform: translateY(-3px);
    }
    @media (max-width: 768px) {
        .contact-header h1 {
            font-size: 1.5rem;
        }
        .contact-body {
            padding: 1.5rem;
        }
    }

    /* Dark Mode */
    [data-theme="dark"] .contact-card {
        background: var(--bg-card);
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    [data-theme="dark"] .info-card {
        background: var(--bg-secondary);
    }
    [data-theme="dark"] .info-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    [data-theme="dark"] .info-card h3 {
        color: var(--text);
    }
    [data-theme="dark"] .info-card p {
        color: var(--text-light);
    }
    [data-theme="dark"] .faq-section h2 {
        color: var(--text);
    }
    [data-theme="dark"] .faq-item {
        border-color: var(--border);
    }
    [data-theme="dark"] .faq-question {
        background: var(--bg-secondary);
        color: var(--text);
    }
    [data-theme="dark"] .faq-question:hover {
        background: var(--bg-card);
    }
    [data-theme="dark"] .faq-answer {
        color: var(--text-light);
        border-top-color: var(--border);
        background: var(--bg-card);
    }
    [data-theme="dark"] .social-links {
        border-top-color: var(--border);
    }
    [data-theme="dark"] .social-link {
        background: var(--bg-secondary);
        color: var(--text-light);
    }
    [data-theme="dark"] .social-link:hover {
        background: #000000;
        color: white;
    }
    [data-theme="dark"] .contact-form input,
    [data-theme="dark"] .contact-form select,
    [data-theme="dark"] .contact-form textarea {
        background: var(--bg-secondary);
        border-color: var(--border);
        color: var(--text);
    }
    [data-theme="dark"] .contact-form label {
        color: var(--text);
    }
    [data-theme="dark"] .contact-form-section h2 {
        color: var(--text);
    }
    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="contact-container">
    <div class="contact-card">
        <div class="contact-header">
            <h1><i class="fas fa-envelope"></i> <?= $translations['contact'] ?? 'Contact' ?></h1>
            <p><?= $translations['we_are_here'] ?? 'We are here to help!' ?></p>
        </div>

        <div class="contact-body">
            <div class="contact-info">
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>E-mail</h3>
                    <p><a href="mailto:info@glamourschedule.nl">info@glamourschedule.nl</a></p>
                </div>
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h3><?= $translations['response_time'] ?? 'Response time' ?></h3>
                    <p><?= $translations['within_24h'] ?? 'Within 24 hours' ?></p>
                </div>
                <div class="info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3><?= $translations['location'] ?? 'Location' ?></h3>
                    <p><?= $translations['netherlands'] ?? 'Netherlands' ?></p>
                </div>
            </div>

            <?php if (!empty($success)): ?>
            <div class="alert alert-success" style="background:#ffffff;border:1px solid #86efac;color:#166534;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="alert alert-error" style="background:#f5f5f5;border:1px solid #e5e5e5;color:#000000;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
            <?php endif; ?>

            <div class="contact-form-section" style="margin-bottom:2rem;">
                <h2 style="color:#374151;font-size:1.3rem;margin-bottom:1rem;">
                    <i class="fas fa-paper-plane" style="color:#000000;margin-right:0.5rem"></i>
                    <?= $translations['send_message'] ?? 'Send us a message' ?>
                </h2>

                <form method="POST" action="/contact" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="form_time" value="<?= time() ?>">

                    <!-- Honeypot field - invisible to users, bots will fill this -->
                    <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;" aria-hidden="true">
                        <label for="website_url">Leave this empty</label>
                        <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                        <div class="form-group">
                            <label style="display:block;margin-bottom:0.5rem;font-weight:500;color:#374151;">
                                <?= $translations['your_name'] ?? 'Name' ?> *
                            </label>
                            <input type="text" name="name" required
                                   value="<?= htmlspecialchars($formData['name'] ?? '') ?>"
                                   style="width:100%;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:10px;font-size:1rem;">
                        </div>
                        <div class="form-group">
                            <label style="display:block;margin-bottom:0.5rem;font-weight:500;color:#374151;">
                                <?= $translations['your_email'] ?? 'Email' ?> *
                            </label>
                            <input type="email" name="email" required
                                   value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                                   style="width:100%;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:10px;font-size:1rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:1rem;">
                        <label style="display:block;margin-bottom:0.5rem;font-weight:500;color:#374151;">
                            <?= $translations['message_type'] ?? 'Type of message' ?> *
                        </label>
                        <select name="type" required style="width:100%;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:10px;font-size:1rem;background:#ffffff;">
                            <option value=""><?= $translations['select_option'] ?? 'Select...' ?></option>
                            <option value="bug" <?= ($formData['type'] ?? '') === 'bug' ? 'selected' : '' ?>>
                                <?= $translations['type_bug'] ?? 'Bug / Report error' ?>
                            </option>
                            <option value="request" <?= ($formData['type'] ?? '') === 'request' ? 'selected' : '' ?>>
                                <?= $translations['type_request'] ?? 'Request / Feature request' ?>
                            </option>
                            <option value="problem" <?= ($formData['type'] ?? '') === 'problem' ? 'selected' : '' ?>>
                                <?= $translations['type_problem'] ?? 'Problem / Need help' ?>
                            </option>
                            <option value="other" <?= ($formData['type'] ?? '') === 'other' ? 'selected' : '' ?>>
                                <?= $translations['type_other'] ?? 'Other' ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:1rem;">
                        <label style="display:block;margin-bottom:0.5rem;font-weight:500;color:#374151;">
                            <?= $translations['subject'] ?? 'Subject' ?> *
                        </label>
                        <input type="text" name="subject" required
                               value="<?= htmlspecialchars($formData['subject'] ?? '') ?>"
                               style="width:100%;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:10px;font-size:1rem;">
                    </div>

                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="display:block;margin-bottom:0.5rem;font-weight:500;color:#374151;">
                            <?= $translations['message'] ?? 'Message' ?> *
                        </label>
                        <textarea name="message" required rows="5"
                                  placeholder="<?= $translations['message_placeholder'] ?? 'Describe your question, problem or request as clearly as possible...' ?>"
                                  style="width:100%;padding:0.75rem 1rem;border:1px solid #e5e7eb;border-radius:10px;font-size:1rem;resize:vertical;"><?= htmlspecialchars($formData['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#000000,#000000);color:white;border:none;border-radius:10px;font-size:1rem;font-weight:600;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;">
                        <i class="fas fa-paper-plane"></i>
                        <?= $translations['send_btn'] ?? 'Send message' ?>
                    </button>
                </form>
            </div>

            <div class="faq-section">
                <h2><i class="fas fa-question-circle" style="color:#000000;margin-right:0.5rem"></i><?= $translations['faq'] ?? 'FAQ' ?></h2>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_cancel_question'] ?? 'How can I cancel an appointment?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?= $translations['faq_cancel_answer'] ?? 'You can cancel your appointment via the link in your confirmation email or by logging into your account. Free cancellation is possible up to 24 hours before the appointment.' ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_confirmation_question'] ?? 'How do I receive my confirmation?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?= $translations['faq_confirmation_answer'] ?? 'After completing your booking, you will immediately receive a confirmation email with all details and a QR code that you can show upon arrival.' ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_payment_question'] ?? 'Which payment methods are accepted?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?= $translations['faq_payment_answer'] ?? 'We accept iDEAL, credit card (Visa, Mastercard), Bancontact and various other payment methods via our secure payment partner Mollie.' ?>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= $translations['faq_salon_owner_question'] ?? 'I am a salon owner, how can I register?' ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <?= $translations['faq_salon_owner_answer'] ?? 'Great! You can register your salon via our <a href="/business/register" style="color:#000000">business registration page</a>. After registration, you can immediately start receiving bookings.' ?>
                    </div>
                </div>
            </div>

            <div class="social-links">
                <a href="#" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFaq(element) {
    const item = element.parentElement;
    item.classList.toggle('active');
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
