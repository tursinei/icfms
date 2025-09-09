<p><span style=""><span style="font-size:14px">Dear {{ $title }} {{ $name }}<br />
{{ $affiliation }}<br />
{{ $country }}</span></span></p>

<p>&nbsp;</p>

<h2><span style="color:#3498db">
    <strong>
        <span style="font-size:calc(var(--scale-factor)*13.92px)">&ldquo;</span>
        <span style="font-size:calc(var(--scale-factor)*13.92px)">The receipt of your</span>
        <span style="font-size:calc(var(--scale-factor)*13.92px)"> </span>
        <span style="font-size:calc(var(--scale-factor)*13.92px)">
            @if ($jenis == 'hotel')
                accomodation
            @else
                registration
            @endif
        </span>
        <span style="font-size:calc(var(--scale-factor)*13.92px)"> </span>
        <span style="font-size:calc(var(--scale-factor)*13.92px)">has been issued&quot;</span>
    </strong>
</span></h2>

<h2>&nbsp;</h2>

<p><span style="font-size:14px"><span style="">Please kindly check your ICFMS account at <a href="{{ url('/') }}">{{ url('/') }}</a><br />
Thank you for your participation and contribution in the ICFMS this year.</span></span></p>
<br/>

<p>See you soon at the conference.</p><br/>

<p><br />
<span style=""><span style="font-size:14px">Best regards,<br />
ICFMS Committee</span></span></p>
