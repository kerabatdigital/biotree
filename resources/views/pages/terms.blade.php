<x-marketing-layout
    title="Terms of Service — BioTree"
    description="The terms and conditions for using BioTree, the link-in-bio service operated from Malaysia.">

    <article class="mx-auto max-w-3xl px-6 py-12 sm:py-16">
        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Terms of Service</h1>
        <p class="mt-3 text-sm text-neutral-500">Last updated: 6 July 2026</p>

        <div class="mt-8 space-y-8 text-[15px] leading-relaxed text-neutral-300
                    [&_h2]:mt-8 [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-white
                    [&_p]:mt-3 [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:pl-6
                    [&_a]:text-emerald-400 [&_a]:underline">

            <section>
                <p>Welcome to BioTree ("BioTree", "we", "us"). These Terms of Service ("Terms") govern your access to and use of the BioTree website at biotree.my and related services (the "Service"). By creating an account or using the Service, you agree to these Terms. If you do not agree, please do not use the Service.</p>
            </section>

            <section>
                <h2>1. The Service</h2>
                <p>BioTree lets you create a single public page ("your page") that hosts your links, and provides related tools such as themes, analytics and QR codes. We may add, change or remove features at any time.</p>
            </section>

            <section>
                <h2>2. Your account</h2>
                <ul>
                    <li>You must provide accurate information and keep your login credentials secure. You are responsible for all activity under your account.</li>
                    <li>You must be old enough to form a binding contract in your jurisdiction to use the Service.</li>
                    <li>You may sign up with an email address or a Google account.</li>
                </ul>
            </section>

            <section>
                <h2>3. Acceptable use</h2>
                <p>You agree not to use the Service to publish or link to content that is unlawful, infringing, misleading, fraudulent, malware-laden, sexually exploitative of minors, or that harasses or endangers others. You must not attempt to disrupt or abuse the Service, scrape it at scale, or circumvent security or rate limits. We may remove content or suspend accounts that violate these Terms.</p>
            </section>

            <section>
                <h2>4. Your content</h2>
                <p>You retain ownership of the content you add to your page. You grant us a licence to host, display and distribute that content solely to operate the Service. You are solely responsible for your content and for having the rights to use it.</p>
            </section>

            <section>
                <h2>5. Usernames</h2>
                <p>Usernames (the <span class="text-neutral-200">biotree.my/you</span> handle) are assigned on a first-come basis and remain our property. Certain names are reserved or designated "premium" and may only be obtained through us. We may reclaim usernames that are inactive, infringe a trademark, or are used in bad faith.</p>
            </section>

            <section>
                <h2>6. Plans, payments &amp; refunds</h2>
                <ul>
                    <li>BioTree offers a Free plan and paid "Pro" plans billed in Malaysian Ringgit (RM).</li>
                    <li>Payments are processed by our third-party provider, toyyibPay. We do not store your full card or banking details.</li>
                    <li>Paid plans grant access for the billing period purchased. Unless required by law, payments are non-refundable; when you cancel, Pro features remain active until the end of the current period.</li>
                    <li>Prices may change; changes apply to future billing periods.</li>
                </ul>
            </section>

            <section>
                <h2>7. Intellectual property</h2>
                <p>The Service, including its software, design and trademarks, is owned by BioTree and protected by law. These Terms do not grant you any right to our branding except as needed to use the Service.</p>
            </section>

            <section>
                <h2>8. Termination</h2>
                <p>You may stop using the Service at any time. We may suspend or terminate your access if you breach these Terms or if we discontinue the Service. On termination, your page may be taken down and associated data may be deleted.</p>
            </section>

            <section>
                <h2>9. Disclaimers &amp; limitation of liability</h2>
                <p>The Service is provided "as is" without warranties of any kind. To the maximum extent permitted by law, BioTree is not liable for indirect, incidental or consequential damages, or for any loss of data, revenue or profits arising from your use of the Service. Our total liability is limited to the amount you paid us in the 12 months before the claim.</p>
            </section>

            <section>
                <h2>10. Governing law</h2>
                <p>These Terms are governed by the laws of Malaysia, and disputes are subject to the exclusive jurisdiction of the Malaysian courts.</p>
            </section>

            <section>
                <h2>11. Changes</h2>
                <p>We may update these Terms from time to time. Material changes will be reflected by the "Last updated" date above. Continued use of the Service after changes take effect means you accept the revised Terms.</p>
            </section>

            <section>
                <h2>12. Contact</h2>
                <p>Questions about these Terms? Email us at <a href="mailto:{{ config('biotree.premium_username_contact') ?? 'support@biotree.my' }}">{{ config('biotree.premium_username_contact') ?? 'support@biotree.my' }}</a>.</p>
            </section>

            <p class="border-t border-neutral-800 pt-6 text-sm text-neutral-500">This document is a general template and not legal advice. Please have it reviewed by a qualified Malaysian lawyer before relying on it commercially.</p>
        </div>
    </article>
</x-marketing-layout>
