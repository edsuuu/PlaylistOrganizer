<div>
    <div>
        <h2>
            {{ __('Planos') }}
        </h2>
    </div>

    <div class="flex flex-row gap-5 justify-center">
        @foreach($plans as $plan)
            {{--        "id" => 1--}}
            {{--        "name" => "Básico"--}}
            {{--        "" => "Saia do caderninho ou planilha e venha para o novo mundo digital."--}}
            {{--        "price_monthly" => "32.80"--}}
            {{--        "price_quarterly" => "98.40"--}}
            {{--        "price_yearly" => "393.00"--}}
            {{--        "features" => "["1 profissional cadastrado", "Até 200 clientes cadastrados", "Link de agendamento com logo Gendo", "Ferramentas de gestão da agenda", "Ferramentas de gestão bá ▶"--}}
            {{--        "trial_days" => 7--}}
            {{--        "active" => 1--}}
            {{--        "created_at" => null--}}
            {{--        "updated_at" => null--}}
            <div class="border border-gray-300 @if($plan->id === 3) border-blue-black  @endif rounded-[10px] bg-white p-4 flex flex-col gap-4 w-[300px]">
                <div class="text-center">
                    <h1 class="font-bold text-2xl">{{ $plan->name }}</h1>
                </div>
                <div class="text-center text-sm">
                    <p>{{ $plan->description }}</p>
                </div>
                <div class="flex flex-col gap-2 ">
            <span class="flex flex-row gap-1 px-3">
                <p class="text-sm">R$</p>
                <span class="flex flex-row">
                    <p class="font-bold text-2xl">{{ number_format($plan->price_monthly, 2, ',', '.') }}</p>

{{--                    <p class="font-bold text-4xl">32</p>--}}
                    {{--                    <p>,99</p>--}}
                </span>
            </span>
                    <small class="text-gray-text text-center">R$ {{ number_format($plan->price_yearly, 2, ',', '.') }} ao
                        ano em até 12x no cartão</small>
                </div>

                {{--        <div--}}
                {{--            class="bg-[#f6f9ff] text-blue-link flex flex-row gap-3 justify-between items-center p-2.5 rounded-[6px]">--}}
                {{--            <div>--}}
                {{--                <img src=" {{ asset('images/card-icon.svg') }}" alt="card" class="w-9">--}}
                {{--            </div>--}}
                {{--            <p class="text-[12px]">Teste grátis por 15 dias sem cadastro de cartão de credito antecipado.</p>--}}
                {{--        </div>--}}
                <button class="border border-blue-hovers text-blue-hovers font-medium py-2 rounded-xl @if($plan->id === 3) bg-blue-hovers text-white @endif hover:bg-blue-hovers hover:text-white transition-all duration-300 ease-in-out">
                    {{ $plan->id === 3 ? "Plano Atual" : "Trocar plano" }}
                </button>
            </div>
        @endforeach
    </div>

</div>
