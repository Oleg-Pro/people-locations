@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Поиск по местоположению</div>

                <div class="panel-body">

                    <form id="search_form" class="form-horizontal" role="form" method="GET" action="{{ route('search') }}">


                        <div class="form-group">
                            <label for="location" class="col-md-4 control-label">Местоположение</label>

                            <div class="col-md-6">
                                <input id="location" type="text" class="form-control" name="location" value="{{$user->location}}" required autofocus>

                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" id="seach">
                                    Поиск
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="map" style="width: 600px; height: 400px"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var defaultLatitude = {{ $user->latitude }},
            defaultLongitude = {{ $user->longitude }},
            myMap;

        $( "#location" ).autocomplete({
            source: 'location-autocomplete',
            select: function( event, ui ) {
                $('#search_form').trigger('submit');
            },
        });

        ymaps.ready(init);


        function init() {
            myMap = new ymaps.Map('map', {
                center: [defaultLatitude, defaultLongitude],
                zoom: 9
            });

            $('#search_form').submit(function() {
                $.getJSON('/search', $(this).serialize()).done(function(users) {
                    display(users);
                });
                return false;
            });

            function display(users) {
                if (users.length) {
                    myMap.geoObjects.removeAll();
                    myMap.setCenter([users[0].latitude, users[0].longitude], 9,  {
                        checkZoomRange: true
                    });

                    for (var i = 0; i < users.length; i++) {
                        myMap.geoObjects.add(new ymaps.Placemark([users[i].latitude, users[i].longitude], {
                            balloonContent:  users[i].first_name + '<br/>' + users[i].second_name
                            + '<br/>' + users[i].last_name + '<br/>' + users[i].email +
                            '<br/>' + '[' + users[i].latitude + ',' + users[i].longitude + ']'
                        }, {
                            preset: 'islands#circleIcon',
                            iconColor: '#3caa3c'
                        }));
                    }
                } else {
                    alert('Нет пользователей из указанного города!');
                }
            }

            $('#search_form').trigger('submit');
        }

    });
</script>
@endsection
