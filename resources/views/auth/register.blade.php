@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Регистрация</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                            <label for="first_name" class="col-md-4 control-label">Имя</label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autofocus>

                                @if ($errors->has('first_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('second_name') ? ' has-error' : '' }}">
                            <label for="second_name" class="col-md-4 control-label">Отчество</label>

                            <div class="col-md-6">
                                <input id="second_name" type="text" class="form-control" name="second_name" value="{{ old('second_name') }}" required autofocus>

                                @if ($errors->has('second_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('second_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                            <label for="last_name" class="col-md-4 control-label">Фамилия</label>

                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required autofocus>

                                @if ($errors->has('last_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Адрес</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Пароль</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Подтверждение пароля</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                            <label for="location" class="col-md-4 control-label">Местоположение</label>

                            <div class="col-md-6">
                                <input id="location" type="text" class="form-control" name="location" value="{{ old('location') }}" required autofocus>

                                @if ($errors->has('location'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('location') }}</strong>
                                    </span>
                                @endif
                                <input type="hidden" id="latitude" name="latitude">
                                <input type="hidden" id="longitude" name="longitude">
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button id="select_location" type="button" class="btn">
                                    Выбрать местоположение
                                </button>
                            </div>
                        </div>


                        <div id="map" style="width: 600px; height: 400px"></div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button id="register" type="submit" class="btn btn-primary" style="margin-top:20px;">
                                    Регистрация
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $( document ).ready(function() {
        var defaultLatitude = 55.753994,//Moscow coordinates by default
            defaultLongitude = 37.622093,
            defaultLocation = 'Москва',
            latitudeElement = $('#latitude'),
            longitudeElement = $('#longitude'),
            locationElement = $('#location'),
            setLocationButton = $('#select_location'),
            registerButton = $('#register'),
            myMap;

        setCoordinateFields(defaultLatitude, defaultLongitude);
        disableRegisterButton();

        setLocationElement(defaultLocation);

        locationElement.keyup(function() {

            if (!locationElement.val().trim()) {
                disableSetLocationButton();
                disableRegisterButton();
            } else {
                enableSetLocationButton();
            }
        });

        ymaps.ready(init);

        function init() {
            myMap = new ymaps.Map('map', {
                center: [defaultLatitude, defaultLongitude],
                zoom: 9
            });

            $('#select_location').click(function() {
               var location = locationElement.val();
               geocode(location);
            });

            myMap.events.add('click', function (e) {
                if (!myMap.balloon.isOpen()) {
                    var coords = e.get('coords');
                    setCoordinateFields(coords[0], coords[1]);
                    reverseGeolocation(coords);
                }
                else {
                    myMap.balloon.close();
                }
            });


            function geocode(location) {
                // Поиск координат центра Нижнего Новгорода.
                ymaps.geocode(location, {
                    results: 1
                }).then(function (res) {
                    enableRegisterButton();
                    var firstGeoObject = res.geoObjects.get(0),
                        coords = firstGeoObject.geometry.getCoordinates(),
                        // Область видимости геообъекта.
                        bounds = firstGeoObject.properties.get('boundedBy');
                    setCoordinateFields(coords[0], coords[1]);
                    myMap.setBounds(bounds, {
                        checkZoomRange: true
                    });
                });
            }

            function reverseGeolocation(coordinates) {
                ymaps.geocode(coordinates, {
                    kind: 'locality',
                    results: 1
                }).then(function (res) {
                    var geoObject = res.geoObjects.get(0);
                    var location = geoObject.properties.get('name');
                    setLocationElement(location);
                    enableRegisterButton();
                });
            }
        }

        function enableRegisterButton() {
            registerButton.prop('disabled', false);
        }

        function disableRegisterButton() {
            registerButton.prop('disabled', true);
        }

         function enableSetLocationButton() {
             setLocationButton.prop('disabled', false);
         }

        function disableSetLocationButton() {
            setLocationButton.prop('disabled', true);
        }

        function setLocationElement(location) {
            locationElement.val(location);
        }


        function setCoordinateFields(latitude, longitude) {
            latitudeElement.val(latitude);
            longitudeElement.val(longitude);
        }

    });
</script>
@endsection
