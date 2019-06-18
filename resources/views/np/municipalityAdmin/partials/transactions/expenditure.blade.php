@if ($expenditure)
<div class="activity__detail" id="activity__expenditure">
    <div class="activity__element__list">
        <h3>
            @lang('lite/title.expenditure')
        </h3>
        <div class="activity__element--info">
            <ul>
                @foreach ($expenditure as $index => $transaction)
                    <li>
                        <span>
                            {{ number_format(round(getVal($transaction, ['transaction', 'value', 0, 'amount']), 2)) }}
                            @if(getVal($transaction, ['transaction', 'value', 0, 'currency']))
                                {{ getVal($transaction, ['transaction', 'value', 0, 'currency']) }}
                            @else
                                {{ $defaultCurrency }}
                            @endif
                            @if(getVal($transaction, ['transaction', 'value', 0, 'date']))
                                [{{ formatDate(getVal($transaction, ['transaction', 'value', 0, 'date'])) }}]
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif