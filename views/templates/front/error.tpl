{extends file='page.tpl'}

{block name='page_content'}
    <div class="text-center my-3">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            viewBox="0 0 174.239 174.239" style="max-width:75px;fill:#E24C4B;" xml:space="preserve">
            <g>
                <path d="M87.12,0C39.082,0,0,39.082,0,87.12s39.082,87.12,87.12,87.12s87.12-39.082,87.12-87.12S135.157,0,87.12,0z M87.12,159.305
                    c-39.802,0-72.185-32.383-72.185-72.185S47.318,14.935,87.12,14.935s72.185,32.383,72.185,72.185S126.921,159.305,87.12,159.305z"
                    />
                <path d="M120.83,53.414c-2.917-2.917-7.647-2.917-10.559,0L87.12,76.568L63.969,53.414c-2.917-2.917-7.642-2.917-10.559,0
                    s-2.917,7.642,0,10.559l23.151,23.153L53.409,110.28c-2.917,2.917-2.917,7.642,0,10.559c1.458,1.458,3.369,2.188,5.28,2.188
                    c1.911,0,3.824-0.729,5.28-2.188L87.12,97.686l23.151,23.153c1.458,1.458,3.369,2.188,5.28,2.188c1.911,0,3.821-0.729,5.28-2.188
                    c2.917-2.917,2.917-7.642,0-10.559L97.679,87.127l23.151-23.153C123.747,61.057,123.747,56.331,120.83,53.414z"/>
            </g>
        </svg>
    </div>
    <header class="page-header">
        <h1 class="h1 text-center">{l s='Authentication error' mod='ps_arengu_auth'}</h1>
    </header>
    <section id="content" class="page-content page-not-found">
        <p class="lead text-center">{$message}</p>
        <div class="d-block text-center mt-4">
        <a class="btn btn-primary" href="{$home_url}">{l s='Go home' mod='ps_arengu_auth'}</a>
        </div>
    </section>
{/block}
