<footer>
    <div class="width-900">
        <div class="social-wrapper bottom-line">
            <div class="col-md-12 text-center">
                <ul>
                    <li><a href="https://github.com/younginnovations/aidstream-new" class="github" title="Fork us on Github">Fork us on Github</a></li>
                    <li><a href="https://twitter.com/aidstream" class="twitter" title="Follow us on Twitter">Follow us on Twitter</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-nav bottom-line">
            <div class="col-md-12">
                <ul>
                    <li><a href="{{ url('/about') }}">About</a></li>
                    <li><a href="{{ url('/who-is-using') }}">Who's using</a></li>
                    <!--<li><a href="#">Snapshot</a></li>-->
                </ul>
                <ul>
                    @if(auth()->check())
                        <li><a href="{{ url((auth()->user()->role_id == 1 || auth()->user()->role_id == 2) ? config('app.admin_dashboard') : config('app.super_admin_dashboard'))}}">Go to Dashboard</a>
                        </li>
                    @else
                        <li><a href="{{ url('/auth/login') }}">Login</a></li>
                        <li><a href="{{ url('/auth/register') }}">Register</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="footer-logo">
            <div class="col-md-12 text-center">
                <a href="{{ url('/') }}"><img src="images/logo-text.png" alt=""></a>
            </div>
        </div>
    </div>
    <div class="width-900 text-center">
        <div class="col-md-12 support-desc">
            For queries, suggestions, shoot us an email at <a href="mailto:support@aidstream.org">support@aidstream.org</a>
        </div>
    </div>
</footer>