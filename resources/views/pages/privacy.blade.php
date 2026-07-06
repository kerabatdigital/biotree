<x-marketing-layout
    title="Privacy Policy — BioTree"
    description="How BioTree collects, uses and protects your personal data, in line with Malaysia's Personal Data Protection Act (PDPA).">

    <article class="mx-auto max-w-3xl px-6 py-12 sm:py-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Privacy Policy</h1>
        <p class="mt-3 text-sm text-neutral-500">Last updated: 6 July 2026</p>

        <div class="mt-8 space-y-8 text-[15px] leading-relaxed text-neutral-300
                    [&_h2]:mt-8 [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-white
                    [&_p]:mt-3 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:pl-6
                    [&_a]:text-emerald-400 [&_a]:underline">

            <section>
                <p>This Privacy Policy explains how BioTree ("we", "us") collects, uses, discloses and protects your personal data when you use biotree.my (the "Service"). We handle personal data in accordance with Malaysia's Personal Data Protection Act 2010 (as amended) ("PDPA"). By using the Service you consent to the practices described here.</p>
            </section>

            <section>
                <h2>1. Data we collect</h2>
                <ul>
                    <li><span class="text-neutral-200">Account data</span> — your name, email address, and (if you sign in with Google) your Google profile basics and avatar. If you buy a plan, a phone number for the payment.</li>
                    <li><span class="text-neutral-200">Page content</span> — the display name, bio, links, images and theme you add to your public page.</li>
                    <li><span class="text-neutral-200">Analytics data</span> — for visits to your public page we record aggregate, privacy-preserving signals such as page views, a hashed visitor identifier, approximate country, device type and referring website. We do not sell this data.</li>
                    <li><span class="text-neutral-200">Payment data</span> — processed by toyyibPay. We receive confirmation and reference details, but not your full card or bank credentials.</li>
                    <li><span class="text-neutral-200">Technical data</span> — IP address and standard log data needed to operate and secure the Service.</li>
                </ul>
            </section>

            <section>
                <h2>2. How we use your data</h2>
                <ul>
                    <li>To create and operate your account and public page.</li>
                    <li>To process payments and manage your subscription.</li>
                    <li>To send essential service emails (verification, receipts, security and account notices).</li>
                    <li>To provide analytics to you about your own page.</li>
                    <li>To protect the Service against fraud, abuse and security threats, and to comply with the law.</li>
                </ul>
            </section>

            <section>
                <h2>3. Third parties we share with</h2>
                <p>We share data only with providers that help us run the Service, under appropriate safeguards:</p>
                <ul>
                    <li><span class="text-neutral-200">Google</span> — optional sign-in.</li>
                    <li><span class="text-neutral-200">toyyibPay</span> — payment processing.</li>
                    <li><span class="text-neutral-200">Resend</span> — sending transactional emails.</li>
                    <li><span class="text-neutral-200">Cloudflare</span> — content delivery and security.</li>
                </ul>
                <p>We do not sell your personal data. We may disclose data if required by law or to protect our rights and users.</p>
            </section>

            <section>
                <h2>4. Cookies</h2>
                <p>We use strictly necessary cookies to keep you logged in and to secure forms. Public pages are designed to load without setting tracking cookies. We do not use third-party advertising cookies.</p>
            </section>

            <section>
                <h2>5. Data retention</h2>
                <p>We keep your data for as long as your account is active. If you delete your account, we delete or anonymise your personal data within a reasonable period, except where we must retain records to comply with legal, tax or accounting obligations.</p>
            </section>

            <section>
                <h2>6. Security</h2>
                <p>We use reasonable technical and organisational measures — including encryption in transit, hashed passwords, access controls and rate limiting — to protect your data. No system is perfectly secure, but we work to keep your data safe.</p>
            </section>

            <section>
                <h2>7. Your rights under the PDPA</h2>
                <p>Subject to the PDPA, you may:</p>
                <ul>
                    <li>Access and request a copy of your personal data.</li>
                    <li>Correct inaccurate or incomplete data.</li>
                    <li>Withdraw consent or limit how we process your data.</li>
                    <li>Request deletion of your account and associated data.</li>
                </ul>
                <p>To exercise these rights, contact us using the details below. Much of this can also be done directly in your account settings.</p>
            </section>

            <section>
                <h2>8. International transfers</h2>
                <p>Some of our providers may process data outside Malaysia. Where this happens, we take steps to ensure your data receives a comparable level of protection.</p>
            </section>

            <section>
                <h2>9. Children</h2>
                <p>The Service is not directed at children under the age required to consent in their jurisdiction. We do not knowingly collect data from such children.</p>
            </section>

            <section>
                <h2>10. Changes</h2>
                <p>We may update this Policy from time to time. Material changes will be reflected by the "Last updated" date above.</p>
            </section>

            <section>
                <h2>11. Contact</h2>
                <p>For any privacy request or question, email <a href="mailto:{{ config('biotree.premium_username_contact') ?? 'support@biotree.my' }}">{{ config('biotree.premium_username_contact') ?? 'support@biotree.my' }}</a>.</p>
            </section>

            <p class="border-t border-neutral-800 pt-6 text-sm text-neutral-500">This document is a general template and not legal advice. Please have it reviewed by a qualified Malaysian lawyer to ensure full PDPA compliance for your business.</p>
        </div>
    </article>
</x-marketing-layout>
