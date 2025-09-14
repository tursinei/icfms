@if ($jenis_dok == 'INVOICE')
    <h3>Method of Payment</h3>
    <p>Payment can be made by Bank Transfer to the following bank account.</p>
    <ul class="norek">
        <li><span class="label">Account Name</span><span class="colon"> :</span><span class="value">Lusi
                Safriani</span></li>
        <li><span class="label">Account Number</span><span class="colon">:</span><span class="value">0022950297</span>
        </li>
        <li><span class="label">Swift code</span><span class="colon"> :</span><span class="value">BNINIDJA</span>
        </li>
        <li><span class="label">Bank Name</span><span class="colon"> :</span><span class="value">BNI UNPAD
                BANDUNG</span></li>
    </ul>
    <p>Please send a confirmation of payment (copy proof of payment) to
        <a href="mailto:lusi.safriani@phys.unpad.ac.id" style="text-decoration: none;"><span class="Hyperlink"
                style="font-weight: bold;">lusi.safriani@phys.unpad.ac.id</span></a>
    </p>
@else
    <p style="font-weight: bold">{{ $date }}</p>
@endif
<img src="css/assetinvtemplate/ttdlogo.png" width="232" height="91" style="margin: 7.05pt;" />
<p style="margin-bottom: 0pt; line-height: normal; width: 20%;"><span
        style="font-weight: bold; text-decoration: underline;">Lusi Safriani</span></p>
<p style="margin-bottom: 25px; line-height: normal;"><span style="font-weight: bold;">Organizing Committee of ICFMS
        {{ date('Y') }}</span>
</p>
