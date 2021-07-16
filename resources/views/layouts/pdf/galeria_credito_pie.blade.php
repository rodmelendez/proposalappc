<div style="clear:both;"></div>
<table style="border:0; border-collapse:collapse; width:100%; {!! !empty($break) ? 'page-break-after:always' : '' !!}">
    <thead>
        @if (!empty($observaciones))
            <tr>
                <td style="text-align:center; border:1px solid #888; border-radius:2px;">
                    <span style="font-size:8pt;">{!! $observaciones !!}</span>
                </td>
            </tr>
        @endif
        @if (!empty($empresa_telefono) || !empty($empresa_website))
            <tr>
                <td style="text-align:center;">
                    @if (!empty($empresa_website))
                        <span style="font-size:8pt;">&nbsp;{!! $empresa_telefono !!}&nbsp;</span>
                    @endif
                    @if (!empty($empresa_website))
                        <span style="font-size:8pt;">&nbsp;{!! $empresa_website !!}&nbsp;</span>
                    @endif
                </td>
            </tr>
        @endif
    </thead>
</table>